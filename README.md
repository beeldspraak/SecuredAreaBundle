# [WIP] Beeldspraak Secured Area Bundle

Secure frontend pages and manage access by connecting pages to users. Integrates with Sonata Admin or other backend admins.

Please note this bundle is temporarily published and will be removed again.

## Dependencies

* [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
* [Symfony CMF Routing Component](https://github.com/symfony-cmf/Routing)

## Basic Authentication and Authorization Flow

* A page is marked to be secured
* (Authentication) When a user requests a page, the `request_matcher` of the `secured_area` firewall will match if the page is secured.
  If yes, the user is directly send to the login form.
* (Authorization) When the user is authenticated the normal routing process is followed. In the controller a `SecurityChecker` is asked
  if the user has access granted.

## Installation

Steps:

1. Download BeeldspraakSecuredAreaBundle using composer
2. Enable the Bundle
3. Update your User class
4. Update your Page class(es)
5. Configure your application's security.yml
6. Implement a SecurityChecker in your controller
7. Implement the backend admin

### Step 1: Download BeeldspraakSecuredAreaBundle using composer

Not applicable (for the moment).

### Step 2: Enable the Bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Beeldspraak\SecuredAreaBundle\BeeldspraakSecuredAreaBundle(),
    );
}
```

### Step 3: Update your User class

Implement the interface `Beeldspraak\SecuredAreaBundle\Model\UserPageableInterface` for your User class.

Example:

``` php
<?php
// Acme/UserBundle/Entity/User.php

use Beeldspraak\SecuredAreaBundle\Model\UserPageableInterface;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface;

class User extends BaseUser implements UserPageableInterface
{
    // ...

    /**
     * @return array
     */
    public function getPageUuids()
    {
        return $this->pageUuids ?: $this->pageUuids = array();
    }

    /**
     * {@inheritDoc}
     */
    public function hasPage($uuid)
    {
        return in_array($uuid, $this->getPageUuids());
    }

    /**
     * {@inheritDoc}
     */
    public function setPages(array $pages)
    {
        $this->pageUuids = array();

        foreach ($pages as $page) {
            if ($page instanceof PageInterface) {
                $this->addPage($page);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addPage(PageInterface $page)
    {
        if (!in_array($page->getUuid(), $this->pageUuids, true)) {
            $this->pageUuids[] = $page->getUuid();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removePage(PageInterface $page)
    {
        if (false !== $key = array_search($page->getUuid(), $this->pageUuids, true)) {
            unset($this->pageUuids[$key]);
            $this->pageUuids = array_values($this->pageUuids);
        }

        return $this;
    }
}
```

### Step 4: Update your Page class(es)

Implement the interface `Beeldspraak\SecuredAreaBundle\Model\PageInterface` for the Page class(es) you would like to secure.

Example:

``` php
<?php
// Acme/ApplicationBundle/Document/Page.php

use Beeldspraak\SecuredAreaBundle\Model\PageInterface as SecuredPageInterface;

class Page implements SecuredPageInterface
{
    // ...

    /**
     * Get universal unique id
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return bool
     */
    public function isSecured()
    {
        return $this->secured;
    }

    // not required by the interface but usefull if you want to set the security state

    /**
     * @param boolean $secured
     */
    public function setSecured($secured)
    {
        $this->secured = $secured;
    }
}
```

### Step 5: Configure your application's security.yml

``` yaml
# app/config/security.yml
security:
   firewalls:
        secured_area:
            switch_user:        true
            context:            user
            request_matcher:    beeldspraak_secured_area.page_request_matcher
            form_login:
                provider:       fos_userbundle
                login_path:     fos_user_security_login
                use_forward:    false
                check_path:     fos_user_security_check
                csrf_provider:  form.csrf_provider
                failure_path:   null
            logout:
                path:           fos_user_security_logout
                target:         /
            anonymous:          true

        main:
            pattern:            ^/
            anonymous:          true
```

### Step 6: Implement a SecurityChecker in your controller

This bundle ships with a SecurityChecker, it has the service id: `beeldspraak_secured_area.access_checker`.

Add it to your default page controller and check if access is granted:

``` php
<?php
// Acme/ApplicationBundle/Controller/DefaultController.php

public function indexAction(Request $request, $contentDocument, $contentTemplate = null)
{
    if ($contentDocument
        && ($this->securityChecker && !$this->securityChecker->checkAccessIsGranted($contentDocument, false))
    ) {
        throw new AccessDeniedException('Access denied for: ' . $request->getPathInfo());
    }

    // ...
}
```

### Step 7: Implement the backend admin

This bundle ships with a FormType, `beeldspraak_security_users`, that displays a list of users and automates the process
to connect pages to users.

By default:
* all users are fetched
* it excludes super admin users, you can change this by setting the option `exclude_super_admin` to `false`
* the UserManager `Beeldspraak\SecuredAreaBundle\Doctrine\UserManager` is used to fetch the users

Add it to your form, fe. with Sonata Admin:

``` php
<?php
// Acme/ApplicationBundle/Admin/PageAdmin.php

public function configureFormFields(FormMapper $formMapper)
{
    $formMapper
        // ...
        ->with('label.security')
            ->add('secured', null, array('required' => false, 'help' => $this->trans('form.help_secured')))
            ->add('users', 'beeldspraak_security_users', array(
                'required'  => false,
                'help'      => $this->trans('form.help_users'),
                'mapped'    => false,
                'page'      => $this->getSubject(),
                'multiple'  => true,
                'expanded'  => true,
            ))
        ->end()
    ;
    // ...
}
```

## Configuration

Default configuration for "BeeldspraakSecuredAreaBundle"
beeldspraak_secured_area:

    # Role to be used for access granted checks.
    role:                 PAGE_VIEW # Example: $securityContext->isGranted('PAGE_VIEW' , $contentDocument);

    # Login routes that should be matched for the "secured area" firewall.
    login_routes:
        login_path:           fos_user_security_login
        check_path:           fos_user_security_check
        logout_path:          fos_user_security_logout
