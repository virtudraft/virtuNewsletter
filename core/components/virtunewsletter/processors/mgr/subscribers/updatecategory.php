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
 */

/**
 * @package virtunewsletter
 * @subpackage processor
 */
if (empty($scriptProperties['subscriberIds'])) {
    return $this->failure($modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id'));
}

if (empty($scriptProperties['categories'])) {
    return $this->failure($modx->lexicon('virtunewsletter.newsletter_err_ns_categories'));
}

$subscriberIds = @explode(',', $scriptProperties['subscriberIds']);
$categories = @explode(',', $scriptProperties['categories']);
foreach ($subscriberIds as $subscriberId) {
    $this->modx->removeCollection('vnewsSubscribersHasCategories', array(
        'subscriber_id' => $subscriberId
    ));
    $subscriber = $modx->getObject('vnewsSubscribers', $subscriberId);
    if ($subscriber) {
        foreach ($categories as $category) {
            $subscriber->setCategory($category);
        }
    }
}
return $this->success();
