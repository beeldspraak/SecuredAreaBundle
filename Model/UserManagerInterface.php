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

namespace Beeldspraak\SecuredAreaBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Interface to be implemented by pageable user managers
 */
interface UserManagerInterface
{
    /**
     * Get users from stash for the given page
     *
     * @param PageInterface $page
     * @return Collection
     */
    public function getStashSecurityUsers(PageInterface $page);

    /**
     * Stash users for the given page
     *
     * @param PageInterface $page
     * @param Collection $users
     * @param bool $force replace users if they exist for the page
     */
    public function stashSecurityUsers(PageInterface $page, Collection $users, $force = false);

    /**
     * Clear stash for page
     *
     * @param PageInterface $page
     */
    public function clearStashSecurityUsers(PageInterface $page);
}
