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

namespace Beeldspraak\SecuredAreaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Beeldspraak\SecuredAreaBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Beeldspraak\SecuredAreaBundle\Form\EventListener\SecurityUsersFieldSubscriber;
use Beeldspraak\SecuredAreaBundle\Model\UserPageableInterface;

class SecurityUsersType extends AbstractType
{
    /** @var UserManagerInterface */
    protected $userManager;

    /** @var array */
    protected $filteredUsers;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(
            new SecurityUsersFieldSubscriber(
                $options['page'],
                $this->getFilteredUsers($options),
                $this->userManager
            )
        );
    }

    /**
     * @param array $options
     * @return UserInterface[]
     */
    public function getFilteredUsers($options)
    {
        if (is_null($this->filteredUsers)) {
            $users = $this->userManager->findUsers();

            foreach ($users as $user) {
                if ($user instanceof UserPageableInterface) {
                    if ($options['exclude_super_admin'] && $user->isSuperAdmin()) {
                        continue;
                    }

                    $this->filteredUsers[] = $user;
                }
            }
        }

        return $this->filteredUsers;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $formType = $this;

        $resolver->setDefaults(array(
            'choice_list' => function (Options $options) use ($formType) {
                return new ObjectChoiceList($formType->getFilteredUsers($options), null, array(), null, 'id');
            },

            'exclude_super_admin' => true,
        ));

        $resolver->setRequired(array('page'));

        $resolver->addAllowedTypes(array(
            'page' => 'Beeldspraak\SecuredAreaBundle\Model\PageInterface'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'beeldspraak_security_users';
    }
}
