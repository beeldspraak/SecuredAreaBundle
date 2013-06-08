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

namespace Beeldspraak\SecuredAreaBundle\Security;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface as SecuredPageInterface;

class SecurityChecker implements SecurityCheckerInterface
{
    /**
     * @var string the role name for the security check
     */
    protected $requiredRole;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param string $requiredRole the role to check with the securityContext
     * @param \Symfony\Component\Security\Core\SecurityContextInterface|null $securityContext
     *      the security context to use to check for the role. No security
     *      check if this is null
     */
    public function __construct($requiredRole, SecurityContextInterface $securityContext = null)
    {
        $this->requiredRole = $requiredRole;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public function checkAccessIsGranted($document)
    {
        if (!($document instanceOf SecuredPageInterface)) {
            return true;
        }

        if (!$document->isSecured()) {
            return true;
        }

        if ($this->securityContext && ($this->securityContext->isGranted('ROLE_SUPER_ADMIN') || $this->securityContext->isGranted('PAGE_VIEW', $document))) {
            return true;
        }

        return false;
    }
}
