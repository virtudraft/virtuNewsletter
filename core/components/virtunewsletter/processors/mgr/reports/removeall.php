<?php

if (empty($scriptProperties['newsletter_id'])) {
    return $this->failure('Missing "newsletter_id"!');
}
$ids = array($scriptProperties['newsletter_id']);
$collection = $modx->getCollection('vnewsNewsletters', array(
    'parent_id' => $scriptProperties['newsletter_id'],
));
if ($collection) {
    foreach ($collection as $item) {
        $ids[] = $item->get('id');
    }
}
$output = $modx->removeCollection('vnewsReports', array(
    'newsletter_id:IN' => $ids,
));

if ($output)
    return $this->success($output);
else
    return $this->failure();