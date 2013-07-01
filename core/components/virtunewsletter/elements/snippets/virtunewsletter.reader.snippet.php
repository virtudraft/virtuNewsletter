<?php

$newsId = $modx->getOption('newsId', $scriptProperties, ($modx->getOption('newsid', $_GET)));
if (intval($newsId) < 1) {
    return;
}

$phsPrefix = $modx->getOption('phsPrefix', $scriptProperties, 'virtuNewsletter.reader.');
$itemTpl = $modx->getOption('itemTpl', $scriptProperties, '@CODE:[[+virtuNewsletter.reader.content]]');

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
$virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter))
    return '';

$virtuNewsletter->setConfigs($scriptProperties);

$newsletter = $virtuNewsletter->getNewsletter($newsId);
$phs = array();
foreach ($newsletter as $k => $v) {
    $phs[$phsPrefix . $k] = $v;
}
$output = $virtuNewsletter->parseTpl($itemTpl, $phs);

return $output;