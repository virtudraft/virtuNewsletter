<?php

$newsId = $scriptProperties['id'];
$newsletter = $modx->getObject('vnewsNewsletters', $newsId);
if (!$newsletter) {
    $modx->setDebug();
    $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:' . $newsId, '', __METHOD__, __FILE__, __LINE__);
    $modx->setDebug(FALSE);
    return $this->failure();
}
$newsletterArray = $newsletter->toArray();

$subscriberArray = array(
    'email' => $scriptProperties['email']
);

if ($newsletterArray['is_recurring']) {
    $ctx = $modx->getObject('modResource', $newsletterArray['resource_id'])->get('context_key');
    $url = $modx->makeUrl($newsletterArray['resource_id'], $ctx, '', 'full');
    if (empty($url)) {
        $modx->setDebug();
        $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get URL for newsletter w/ resource_id:' . $newsletterArray['resource_id'], '', __METHOD__, __FILE__, __LINE__);
        $modx->setDebug(FALSE);
        return $this->failure();
    }
    $newsletterArray['content'] = file_get_contents($url);
}

$confirmLinkArgs = $modx->virtunewsletter->confirmationLinkArguments($subscriberArray['email']);
if ($confirmLinkArgs) {
    $confirmLinkArgs = array_merge($confirmLinkArgs, array('act' => 'unsubscribe'));
    $modx->virtunewsletter->setPlaceholder('unsubscribeLink', $confirmLinkArgs);
}
$modx->virtunewsletter->setPlaceholder('subscriber', $subscriberArray);
$phs = $modx->virtunewsletter->getPlaceholders();
$output = $modx->virtunewsletter->sendMail($newsletterArray['subject'], $newsletterArray['content'], $subscriberArray['email'], $phs);
return $this->success($output);