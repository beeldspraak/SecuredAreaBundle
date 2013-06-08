<?php
/**
 * This file and its content is copyright of Beeldspraak Website Creators BV - (c) Beeldspraak 2012. All rights reserved.
 * Any redistribution or reproduction of part or all of the contents in any form is prohibited.
 * You may not, except with our express written permission, distribute or commercially exploit the content.
 *
 * @author     Beeldspraak <info@beeldspraak.com>
 * @copyright  Copyright 2012, Beeldspraak Website Creators BV
 * @link       http://beeldspraak.com
 */

namespace Beeldspraak\SecuredAreaBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface;
use Beeldspraak\SecuredAreaBundle\Model\UserPageableInterface;

class PageVoter implements VoterInterface
{
    /** @var string */
    protected $requiredRole;

    public function __construct($requiredRole)
    {
        $this->requiredRole = $requiredRole;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, 'Beeldspraak\SecuredAreaBundle\Model\PageInterface');
    }

    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return $attribute === $this->requiredRole;
    }

    /**
     * Checks if the voter supports the user of the given token
     *
     * @param TokenInterface $token
     *
     * @return bool true if this Voter can process the user
     */
    public function supportsUser(TokenInterface $token)
    {
        if (!is_object($token->getUser())) {
            return false;
        }

        return is_subclass_of(get_class($token->getUser()), 'Beeldspraak\SecuredAreaBundle\Model\UserPageableInterface');
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object)) || !$this->supportsUser($token)) {
            return self::ACCESS_ABSTAIN;
        }

        /** @var PageInterface $object */

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            if ($token->getUser()->hasPage($object->getUuid())) {
                return VoterInterface::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        // no supported attribute, do not vote
        return self::ACCESS_ABSTAIN;
    }
}
