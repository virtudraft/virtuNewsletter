<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2023 by goldsky <goldsky@virtudraft.com>
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

$toArray = $modx->getOption('toArray', $scriptProperties);
$activeOnly = $modx->getOption('activeOnly', $scriptProperties, 1);
$readerPage = $modx->getOption('readerPage', $scriptProperties, $modx->getOption('virtunewsletter.readerpage'));

$phsPrefix = $modx->getOption('phsPrefix', $scriptProperties, 'virtuNewsletter.list.');
$itemTpl = $modx->getOption('itemTpl', $scriptProperties, 'virtunewsletter.list.item');
$itemSeparator = $modx->getOption('itemSeparator', $scriptProperties, "\n");
$wrapperTpl = $modx->getOption('wrapperTpl', $scriptProperties, 'virtunewsletter.list.wrapper');

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
$virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter))
    return '';

$output = '';
// test
//$toArray = 1;

$c = $modx->newQuery('vnewsNewsletters');
$c->where(array(
    'scheduled_for:<' => time(),
    'is_recurring:!=' => 1,
));
if (!empty($activeOnly)) {
    $c->where(array(
        'is_active' => 1
    ));
}
$newsletters = $modx->getCollection('vnewsNewsletters', $c);
if ($newsletters) {
    $itemArray = array();

    $email = '';
    if ($modx->user->isAuthenticated()) {
        $userProfile = $modx->user->getOne('Profile');
        if ($userProfile) {
            $email = $userProfile->get('email');
        }
    }

    foreach ($newsletters as $newsletter) {
        $newsletterArray = $newsletter->toArray();
        $args = 'newsid=' . $newsletterArray['id'] . (!empty($email) ? '&e=' . $email : '');
        $newsletterArray['readerpage'] = $modx->makeUrl($readerPage, '', $args, 'full');
        $phs = $virtuNewsletter->setPlaceholders($newsletterArray, $phsPrefix);

        if ($toArray) {
            $itemArray[] = $phs;
        } else {
            $itemString = $virtuNewsletter->parseTpl($itemTpl, $phs);
            $itemString = $virtuNewsletter->processElementTags($itemString);
            $itemArray[] = $itemString;
        }
    }

    if ($toArray) {
        $wrapper = array(
            $phsPrefix . 'items' => $itemArray
        );
        $output = '<pre>' . print_r($wrapper, TRUE) . '</pre>';
    } else {
        $outputString = @implode($itemSeparator, $itemArray);
        $wrapper = array(
            $phsPrefix . 'items' => $outputString
        );
        $wrapperOutput = $virtuNewsletter->parseTpl($wrapperTpl, $wrapper);
        $output = $virtuNewsletter->processElementTags($wrapperOutput);
    }
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
    return;
}

return $output;
