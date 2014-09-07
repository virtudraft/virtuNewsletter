<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2014 by goldsky <goldsky@virtudraft.com>
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
$scriptProperties['emailKey'] = $modx->getOption('emailKey', $scriptProperties, 'email');
if (!isset($_POST[$scriptProperties['emailKey']])) {
    $errorPage = $modx->getOption('error_page');
    $url = $modx->makeUrl($errorPage, NULL, '', 'full');
    $modx->sendRedirect($url);
}

$scriptProperties['phsPrefix'] = $modx->getOption('phsPrefix', $scriptProperties, 'virtuNewsletter.email.');
$scriptProperties['nameKey'] = $modx->getOption('nameKey', $scriptProperties, 'name');
$scriptProperties['categoryKey'] = $modx->getOption('categoryKey', $scriptProperties, 'category');
$emailTo = $_POST[$scriptProperties['emailKey']];
$emailFrom = $modx->getOption('emailFrom', $scriptProperties);
$emailFromName = $modx->getOption('emailFromName', $scriptProperties);

$successMsg = $modx->getOption('successMsg', $scriptProperties);

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
$virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter))
    return '';

$result = $virtuNewsletter->subscribe($_POST);

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
    $template = $modx->getObject('vnewsTemplates', array(
        'name' => 'subscribing',
        'culture_key' => $modx->cultureKey
    ));
    if ($template) {
        $subject = $virtuNewsletter->processElementTags($template->get('subject'));
        $message = $template->get('content');
        $message = $virtuNewsletter->parseTpl('@CODE:' . $message, $phs);
        $message = $virtuNewsletter->processElementTags($message);
        $virtuNewsletter->sendMail($subject, $message, $emailTo, $emailFrom, $emailFromName);
    } else {
        // fallback < 1.6.0-beta2
        $resourceId = $modx->getOption('virtunewsletter.subscribe_confirmation_tpl');
        $resource = $modx->getObject('modResource', $resourceId);
        if (!$resource) {
            $modx->setDebug();
            $modx->log(modX::LOG_LEVEL_ERROR, 'Missing resource tpl for email confirmation');
            $modx->setDebug(FALSE);
        } else {
            $subject = $virtuNewsletter->processElementTags($resource->get('pagetitle'));
            $message = $resource->get('content');
            $message = $virtuNewsletter->parseTpl('@CODE:' . $message, $phs);
            $message = $virtuNewsletter->processElementTags($message);
            $virtuNewsletter->sendMail($subject, $message, $emailTo, $emailFrom, $emailFromName);
        }
    }
    unset($_POST); // destroy submission.
} else {
    $output = $modx->lexicon('virtunewsletter.subscriber_err_proc');
}

return $output;