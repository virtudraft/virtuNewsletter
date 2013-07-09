<?php

if (empty($scriptProperties['newsletter_id'])) {
    return $this->failure('Missing "newsletter_id"!');
}

$output = $modx->removeCollection('vnewsReports', array(
    'newsletter_id' => $scriptProperties['newsletter_id']
));

if ($output)
    return $this->success($output);
else
    return $this->failure();