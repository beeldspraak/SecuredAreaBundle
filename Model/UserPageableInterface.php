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

/**
 * Interface to be implemented by user models when secured pages are used.
 */
interface UserPageableInterface
{
    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('PAGE_VIEW');
     *
     * @param string $uuid
     *
     * @return boolean
     */
    public function hasPage($uuid);

    /**
     * Sets the pages of the user.
     *
     * This overwrites any previous pages.
     *
     * @param array $pages
     *
     * @return self
     */
    public function setPages(array $pages);

    /**
     * Adds a page to the user.
     *
     * @param PageInterface $page
     *
     * @return self
     */
    public function addPage(PageInterface $page);

    /**
     * Removes a role to the user.
     *
     * @param PageInterface $page
     *
     * @return self
     */
    public function removePage(PageInterface $page);
}
