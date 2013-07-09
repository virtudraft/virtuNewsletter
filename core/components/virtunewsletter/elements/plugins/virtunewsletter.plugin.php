<?php

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
    default :

        break;
}
return;