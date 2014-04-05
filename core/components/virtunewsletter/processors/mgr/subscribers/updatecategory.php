<?php

if (empty($scriptProperties['subscriberId'])) {
    return $this->failure($modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id'));
}

if (empty($scriptProperties['categories'])) {
    return $this->failure($modx->lexicon('virtunewsletter.newsletter_err_ns_categories'));
}

$this->modx->removeCollection('vnewsSubscribersHasCategories', array(
    'subscriber_id' => $scriptProperties['subscriberId']
));
$categories = @explode(',', $scriptProperties['categories']);
$subscriber = $modx->getObject('vnewsSubscribers', $scriptProperties['subscriberId']);
foreach ($categories as $category) {
    if ($subscriber) {
        $subscriber->setCategory($category);
    }
}
return $this->success();