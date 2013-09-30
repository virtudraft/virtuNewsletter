<?php

$systemEmailPrefix = $modx->getOption('virtunewsletter.email_prefix');
$modx->virtunewsletter->setPlaceholder('id', $scriptProperties['id'], $systemEmailPrefix);
$newsletter = $modx->getObject('vnewsNewsletters', $scriptProperties['id']);
if (!$newsletter) {
    $modx->setDebug();
    $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:' . $scriptProperties['id'], '', __METHOD__, __FILE__, __LINE__);
    $modx->setDebug(FALSE);
    return $this->failure();
}

$newsletterArray = $newsletter->toArray();

$subscriber = $modx->getObject('vnewsSubscribers', array(
    'email' => $scriptProperties['email']
        ));
if ($subscriber) {
    $subscriberArray = $subscriber->toArray();
} else {
    $subscriberArray = array(
        'email' => $scriptProperties['email']
    );
}

$confirmLinkArgs = $modx->virtunewsletter->getSubscriber(array('email' => $subscriberArray['email']));
if ($confirmLinkArgs) {
    $confirmLinkArgs = array_merge($confirmLinkArgs, array('act' => 'unsubscribe'));
    $modx->virtunewsletter->setPlaceholders($confirmLinkArgs, $systemEmailPrefix);
}
$modx->virtunewsletter->setPlaceholders(array_merge($subscriberArray, array('id' => $newsletterArray['id'])), $systemEmailPrefix);
$phs = $modx->virtunewsletter->getPlaceholders();
$output = $modx->virtunewsletter->sendMail($newsletterArray['subject'], $newsletterArray['content'], $subscriberArray['email'], $phs);
return $this->success($output);