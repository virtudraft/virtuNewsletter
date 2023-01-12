<?php

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path').'components/virtunewsletter/';
$virtuNewsletterCorePath        = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter                = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath.'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter)) {
    return '';
}

$output = '';
if ($modx->user->isAuthenticated($modx->context->get('key'))) {
    $subscribed = $modx->getObject('vnewsSubscribers', array(
        'user_id' => $modx->user->get('id')
    ));
    if ($subscribed) {
        $output = $subscribed->get('email');
    }
}

return $output;