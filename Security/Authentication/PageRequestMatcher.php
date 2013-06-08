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

namespace Beeldspraak\SecuredAreaBundle\Security\Authentication;

use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Beeldspraak\SecuredAreaBundle\Model\PageInterface;

class PageRequestMatcher implements RequestMatcherInterface
{
    /** @var array */
    protected $loginRoutes;

    /** @var  */
    protected $dm;

    /**
     * @param array $loginRoutes the login routes to also match
     */
    public function __construct(DocumentManager $dm, array $loginRoutes = array())
    {
        $this->loginRoutes = $loginRoutes;
        $this->dm = $dm;
    }

    /**
     * {@inheritDoc}
     */
    public function matches(Request $request)
    {
        /** @var RouteObjectInterface $route */
        $routeDocument = $request->get('routeDocument');
        $route = $request->get('_route');

        // We need to set the locale here, for some reason we haven't found yet
        // The DM doesn't has the locale injected yet
        $this->dm->getLocaleChooserStrategy()->setLocale($request->getLocale());

        if ($routeDocument && $routeDocument instanceof RouteObjectInterface) {
            /** @var RouteObjectInterface $route  */
            $routeContent = $routeDocument->getRouteContent();

            if ($routeContent
                && $routeContent instanceof PageInterface
                && $routeContent->isSecured()
            ) {
                return true;
            }
        } elseif ($route && in_array($route, $this->loginRoutes)) {
            return true;
        }

        return false;
    }
}
