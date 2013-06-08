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

namespace Beeldspraak\SecuredAreaBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface;
use Beeldspraak\SecuredAreaBundle\Model\UserPageableInterface;
use Beeldspraak\SecuredAreaBundle\Model\UserManagerInterface;

class SecurityUsersFieldSubscriber implements EventSubscriberInterface
{
    /** @var PageInterface */
    protected $page;

    /** @var array */
    protected $users;

    /** @var UserManagerInterface */
    protected $userManager;

    /**
     * @param string $pageUuid
     * @param array $users
     */
    public function __construct(PageInterface $page, array $users = array(), UserManagerInterface $userManager)
    {
        $this->page = $page;
        $this->users = $users;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_BIND => 'postBind',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null !== $data) {
            return;
        }

        $data = array();

        foreach ($this->users as $user) {
            /** @var UserPageableInterface $user */

            if ($user->hasPage($this->page->getUuid())) {
                $data[] = $user;
            }
        }

        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function postBind(FormEvent $event)
    {
        $form = $event->getForm();

        $selectedUsers = new ArrayCollection($form->getData());

        // stash selected users, the doctrine event listener will get them from stash
        // when the page object is persisted or updated
        $this->userManager->stashSecurityUsers($this->page, $selectedUsers, true);
    }
}
