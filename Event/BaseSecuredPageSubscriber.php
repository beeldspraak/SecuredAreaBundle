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

namespace Beeldspraak\SecuredAreaBundle\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\Common\Events;
use Doctrine\Common\Persistence\ObjectManager;
use Beeldspraak\SecuredAreaBundle\Model\UserManagerInterface;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface;

abstract class BaseSecuredPageSubscriber implements EventSubscriber
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var UserManagerInterface */
    protected $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(ObjectManager $objectManager, UserManagerInterface $userManager)
    {
        $this->objectManager = $objectManager;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
        );
    }

    /**
     * @abstract
     *
     * @param EventArgs $args
     *
     * @return PageInterface
     */
    abstract protected function getPage(EventArgs $args);

    /**
     * @param EventArgs $args
     */
    public function postPersist(EventArgs $args)
    {
        $this->onSecurePage($args);
    }

    /**
     * @param EventArgs $args
     */
    public function postUpdate(EventArgs $args)
    {
        $this->onSecurePage($args);
    }

    /**
     * @param EventArgs $args
     */
    public function onSecurePage(EventArgs $args)
    {
        /** @var PageInterface $page */
        $page = $this->getPage($args);

        if (!$page instanceof PageInterface) {
            return;
        }

        // get users bound to the page and stashed when fe. a form is posted
        $securityUsers = $this->userManager->getStashSecurityUsers($page);

        if (!$securityUsers) {
            return;
        }

        // update users
        foreach ($this->userManager->findUsers() as $user) {
            if ($securityUsers && $securityUsers->contains($user)) {
                $user->addPage($page);
                $this->objectManager->persist($user);
            } elseif ($user->hasPage($page->getUuid())) {
                $user->removePage($page);
                $this->objectManager->persist($user);
            }
        }

        $this->userManager->clearStashSecurityUsers($page);

        $this->objectManager->flush();
    }

    /**
     * @param EventArgs $args
     */
    public function preRemove(EventArgs $args)
    {
        /** @var PageInterface $page */
        $page = $this->getPage($args);

        if (!$page instanceof PageInterface) {
            return;
        }

        // update users
        foreach ($this->userManager->findUsers() as $user) {
            if ($user->hasPage($page->getUuid())) {
                $user->removePage($page);
                $this->objectManager->persist($user);
            }
        }

        $this->userManager->clearStashSecurityUsers($page);

        $this->objectManager->flush();
    }
}
