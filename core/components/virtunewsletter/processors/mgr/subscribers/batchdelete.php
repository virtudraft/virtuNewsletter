<?php

if (empty($scriptProperties['ids'])) {
    return $this->failure($modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id'));
}

$ids = @explode(',', $scriptProperties['ids']);
foreach ($ids as $id) {
    $subscriber = $modx->getObject('vnewsSubscribers', $id);
    if ($subscriber) {
        $subscriber->remove();
    }
}
return $this->success();
