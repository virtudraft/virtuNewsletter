<?php

$c = $modx->newQuery('vnewsReports');
$c->where(array(
    'newsletter_id' => $scriptProperties['newsletter_id'],
    'status' => 'queue'
));
//$limit = $modx->getOption('virtunewsletter.email_limit');
//$c->limit($limit);
$queues = $modx->getCollection('vnewsReports', $c);
if ($queues) {
    foreach ($queues as $queue) {
        $sent = $modx->virtunewsletter->sendNewsletter($scriptProperties['newsletter_id'], $queue->get('subscriber_id'));
        if ($sent) {
            $queue->set('status_logged_on', time());
            $nextOccurrenceTime = $queue->get('next_occurrence_time');
            if (!empty($nextOccurrenceTime)) {
                $queue->set('current_occurrence_time', $nextOccurrenceTime);
                $nextOccurrenceTime = $modx->virtunewsletter->nextOccurrenceTime($scriptProperties['newsletter_id']);
                $queue->set('next_occurrence_time', $nextOccurrenceTime);
            } else {
                $queue->set('status', 'sent');
            }
            if ($queue->save() === FALSE) {
                $modx->setDebug();
                $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                $modx->setDebug(FALSE);
            }
        } else {
            $modx->setDebug();
            $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
            $modx->setDebug(FALSE);
        }
    }
}

return $this->success();