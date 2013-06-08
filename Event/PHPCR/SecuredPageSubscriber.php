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

namespace Beeldspraak\SecuredAreaBundle\Event\PHPCR;

use Doctrine\Common\EventArgs;
use Doctrine\ODM\PHPCR\Event;
use Beeldspraak\SecuredAreaBundle\Event\BaseSecuredPageSubscriber;

class SecuredPageSubscriber extends BaseSecuredPageSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Event::postPersist,
            Event::postUpdate,
            Event::preRemove,
        );
    }

    /**
     * @inheritdoc
     */
    protected function getPage(EventArgs $args)
    {
        return $args->getDocument();
    }
}
