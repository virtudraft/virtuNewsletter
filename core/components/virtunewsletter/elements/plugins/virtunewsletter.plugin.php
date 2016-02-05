<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2016 by goldsky <goldsky@virtudraft.com>
 *
 * This file is part of virtuNewsletter, a newsletter system for MODX
 * Revolution.
 *
 * virtuNewsletter is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation version 3,
 *
 * virtuNewsletter is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * virtuNewsletter; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package virtunewsletter
 * @subpackage plugin
 */
$eventName = $modx->event->name;
switch ($eventName) {
    case 'OnUserRemove':
        $defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
        $virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
        $virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/');

        if (!($virtuNewsletter instanceof VirtuNewsletter))
            return '';

        $subscriber = $modx->getObject('vnewsSubscribers', array(
            'user_id' => $user->get('id')
        ));
        if ($subscriber) {
            $subscriber->remove();
        }

        break;
    case 'OnUserActivate':
        $defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
        $virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
        $virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/');

        if (!($virtuNewsletter instanceof VirtuNewsletter))
            return '';

        $userId = $user->get('id');
        $subscriber = $modx->getObject('vnewsSubscribers', array(
            'user_id' => $userId
        ));
        if ($subscriber) {
            $subscriber->set('is_active', 1);
        } else {
            $subscriber = $modx->newObject('vnewsSubscribers');
            $fullname = $user->getOne('Profile')->get('fullname');
            $name = !empty($fullname) ? $fullname : $user->get('username');
            $email = $user->getOne('Profile')->get('email');
            $subscriber->fromArray(array(
                'user_id' => $userId,
                'email' => $email,
                'name' => $name,
                'is_active' => 1,
                'hash' => $virtuNewsletter->setHash($email),
            ));
        }
        $subscriber->save();
        break;
    case 'OnUserDeactivate':
        $defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
        $virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
        $virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/');

        if (!($virtuNewsletter instanceof VirtuNewsletter))
            return '';

        $userId = $user->get('id');
        $subscriber = $modx->getObject('vnewsSubscribers', array(
            'user_id' => $userId
        ));
        if ($subscriber) {
            $subscriber->set('is_active', 0);
            $subscriber->save();
        }

        break;
    case 'OnDocFormSave':
        $defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
        $virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
        $virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/');

        if (!($virtuNewsletter instanceof VirtuNewsletter))
            return '';

        $resourceId = $resource->get('id');
        $newsletter = $modx->getObject('vnewsNewsletters', array(
            'resource_id' => $resourceId
        ));
        if ($newsletter) {
            $content = $virtuNewsletter->outputContent($resourceId);
            $isRecurring = $newsletter->get('is_recurring');
            if ($isRecurring) {
                $recurringNewsletter = $virtuNewsletter->createNextRecurrence($newsletter->get('id'));
                $content = $virtuNewsletter->prepareEmailContent($recurringNewsletter['content']);
            } else {
                $content = $virtuNewsletter->prepareEmailContent($content);
            }
            $content = str_replace(array('%5B%5B%2B', '%5D%5D'), array('[[+', ']]'), $content);
            $newsletter->set('content', $content);
            $newsletter->save();
        }

        break;
    default :

        break;
}
return;
