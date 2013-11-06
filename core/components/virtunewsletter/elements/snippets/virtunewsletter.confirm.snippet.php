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
if (!isset($_GET['subid']) ||
        empty($_GET['subid']) ||
        !isset($_GET['h']) ||
        empty($_GET['h']) ||
        !isset($_GET['act']) ||
        empty($_GET['act'])
        ) {
    $errorPage = $modx->getOption('error_page');
    $url = $modx->makeUrl($errorPage, NULL, '', 'full');
    $modx->sendRedirect($url);
}

$scriptProperties['phsPrefix'] = $modx->getOption('phsPrefix', $scriptProperties, 'virtuNewsletter.email.');
$successMsg = $modx->getOption('successMsg', $scriptProperties);
$emailFrom = $modx->getOption('emailFrom', $scriptProperties);
$emailFromName = $modx->getOption('emailFromName', $scriptProperties);

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
$virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter))
    return '';

$result = $virtuNewsletter->confirmAction(array(
    'id' => intval($_GET['subid']),
    'hash' => $_GET['h'],
    'action' => $_GET['act']
));

$output = '';
if ($result === FALSE) {
    $output = $virtuNewsletter->getError();
} elseif ($result === TRUE) {
    if (empty($successMsg)) {
        $output = $virtuNewsletter->getOutput();
    } else {
        $output = $successMsg;
    }
    $phs = $virtuNewsletter->getPlaceholders();
    if ($_GET['act'] === 'subscribe') {
        $resourceId = $modx->getOption('virtunewsletter.subscribe_succeeded_tpl');
    } elseif ($_GET['act'] === 'unsubscribe') {
        $resourceId = $modx->getOption('virtunewsletter.unsubscribe_succeeded_tpl');
    } else {
        $resourceId = 0;
    }
    $resource = $modx->getObject('modResource', $resourceId);
    if (!$resource) {
        $modx->setDebug();
        $modx->log(modX::LOG_LEVEL_ERROR, 'Missing resource tpl for confirmation report : ' . $resourceId, '', __METHOD__, __FILE__, __LINE__);
        $modx->setDebug(FALSE);
    } else {
        $subject = $virtuNewsletter->processElementTags($resource->get('pagetitle'));
        $message = $resource->get('content');
        $message = $virtuNewsletter->parseTpl('@CODE:' . $message, $phs);
        $message = $virtuNewsletter->processElementTags($message);
        $virtuNewsletter->sendMail($subject, $message, $phs[$scriptProperties['phsPrefix'] . 'emailTo'], $emailFrom, $emailFromName);
    }
} else {
    $output = $modx->lexicon('virtunewsletter.confirm_err_proc');
}

return $output;