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

namespace Beeldspraak\SecuredAreaBundle\Doctrine;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Beeldspraak\SecuredAreaBundle\Model\UserManagerInterface;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface;

class UserManager extends BaseUserManager implements UserManagerInterface
{
    /** @var array */
    protected $stashSecurityUsers;

    /**
     * {@inheritdoc}
     */
    public function getStashSecurityUsers(PageInterface $page)
    {
        $hash = spl_object_hash($page);

        return isset($this->stashSecurityUsers[$hash]) ? $this->stashSecurityUsers[$hash] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function stashSecurityUsers(PageInterface $page, Collection $users, $force = false)
    {
        $hash = spl_object_hash($page);

        if (isset($this->stashSecurityUsers[$hash]) && false === $force) {
            return;
        }

        $this->stashSecurityUsers[$hash] = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function clearStashSecurityUsers(PageInterface $page)
    {
        $hash = spl_object_hash($page);

        if (isset($this->stashSecurityUsers[$hash])) {
            unset($this->stashSecurityUsers[$hash]);
        }
    }
}
