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
$toArray    = $modx->getOption('toArray', $scriptProperties);
$activeOnly = $modx->getOption('activeOnly', $scriptProperties, 1);
$ids        = $modx->getOption('ids', $scriptProperties);
$names      = $modx->getOption('names', $scriptProperties);
$sortBy     = $modx->getOption('sortBy', $scriptProperties, 'sort_index');
$sortDir    = $modx->getOption('sortDir', $scriptProperties, 'asc');
$checkEmail = $modx->getOption('checkEmail', $scriptProperties);

$phsPrefix     = $modx->getOption('phsPrefix', $scriptProperties, 'virtuNewsletter.category.');
$itemTpl       = $modx->getOption('itemTpl', $scriptProperties, 'category/list/item');
$itemSeparator = $modx->getOption('itemSeparator', $scriptProperties, "\n");
$wrapperTpl    = $modx->getOption('wrapperTpl', $scriptProperties, 'category/list/wrapper');

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path').'components/virtunewsletter/';
$virtuNewsletterCorePath        = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter                = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath.'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter)) {
    return '';
}

$c = $modx->newQuery('vnewsCategories');
if (!empty($ids)) {
    $ids = array_map('trim', @explode(',', $ids));
    $c->where(array(
        'id:IN' => $ids
    ));
}
if (!empty($names)) {
    $names = array_map('trim', @explode(',', $names));
    $c->where(array(
        'name:IN' => $names
    ));
}
$c->sortby($sortBy, $sortDir);

//if (!empty($activeOnly)) {
//    $c->where(array(
//        'is_active' => 1
//    ));
//}

$categories = $modx->getCollection('vnewsCategories', $c);
if (!$categories) {
    return;
}

$myCats = array();
if (!empty($checkEmail)) {
    $c = $modx->newQuery('vnewsSubscribersHasCategories');
    $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsSubscribersHasCategories.subscriber_id');
    $c->where(array(
        'Subscribers.email:LIKE' => $checkEmail,
        'vnewsSubscribersHasCategories.unsubscribed_on' => NULL,
    ));

    $mySubCats = $modx->getCollection('vnewsSubscribersHasCategories', $c);
    if ($mySubCats) {
        foreach ($mySubCats as $mySubCat) {
            $myCats[$mySubCat->get('category_id')] = $mySubCat->get('category_id');
        }
    }

} else if ($modx->user->isAuthenticated($modx->context->get('key'))) {
//    $userGroups = $modx->user->getUserGroups();

    $c = $modx->newQuery('vnewsSubscribersHasCategories');
    $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsSubscribersHasCategories.subscriber_id');
    $c->where(array(
        'Subscribers.user_id' => $modx->user->get('id'),
        'vnewsSubscribersHasCategories.unsubscribed_on' => NULL,
    ));

    $mySubCats = $modx->getCollection('vnewsSubscribersHasCategories', $c);
    if ($mySubCats) {
        foreach ($mySubCats as $mySubCat) {
            $myCats[$mySubCat->get('category_id')] = $mySubCat->get('category_id');
        }
    }
}

foreach ($categories as $category) {
    $categoryArray = $category->toArray();

    $categoryArray['is_subscribed'] = '';
    if (!empty($myCats)) {
        if (in_array($categoryArray['id'], $myCats)) {
            $categoryArray['is_subscribed'] = true;
        }
    }

    $phs = $virtuNewsletter->setPlaceholders($categoryArray, $phsPrefix);

    if ($toArray) {
        $itemArray[] = $phs;
    } else {
        $itemString  = $virtuNewsletter->parseTpl($itemTpl, $phs);
        $itemString  = $virtuNewsletter->processElementTags($itemString);
        $itemArray[] = $itemString;
    }
}

if ($toArray) {
    $wrapper = array(
        $phsPrefix.'items' => $itemArray
    );
    $output  = '<pre>'.print_r($wrapper, TRUE).'</pre>';
} else {
    $outputString  = @implode($itemSeparator, $itemArray);
    $wrapper       = array(
        $phsPrefix.'items' => $outputString
    );
    $wrapperOutput = $virtuNewsletter->parseTpl($wrapperTpl, $wrapper);
    $output        = $virtuNewsletter->processElementTags($wrapperOutput);
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
    return;
}

return $output;
