<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013 by goldsky <goldsky@virtudraft.com>
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
 * @subpackage snippet
 */
$newsId = !empty($scriptProperties['newsid']) && is_numeric($scriptProperties['newsid']) ? intval($scriptProperties['newsid']) :
        (isset($_GET['newsid']) && is_numeric($_GET['newsid']) ? intval($_GET['newsid']) : '');
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
if (!$newsletter) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:' . $newsId);
    return FALSE;
}
if ($newsletter['is_recurring']) {
    $ctx = $modx->getObject('modResource', $newsletter['resource_id'])->get('context_key');
    $url = $modx->makeUrl($newsletter['resource_id'], $ctx, '', 'full');
    if (empty($url)) {
        return FALSE;
    }
    $newsletter['content'] = file_get_contents($url);
}
$newsletter['content'] = str_replace('%5B%5B%2B', '[[+', $newsletter['content']);
$newsletter['content'] = str_replace('%5D%5D', ']]', $newsletter['content']);

$subscriberEmail = isset($_GET['e']) ? $_GET['e'] : '';
$subscriber = $virtuNewsletter->getSubscriber(array('email' => $subscriberEmail));

$phs = $virtuNewsletter->setPlaceholders(array_merge($newsletter, $subscriber), $phsPrefix);
$phs = $virtuNewsletter->getPlaceholders();
$output = $virtuNewsletter->parseTpl($itemTpl, $phs);

return $output;