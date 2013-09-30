<?php

$c = $modx->newQuery('vnewsReports');
$c->leftJoin('vnewsNewsletters', 'vnewsNewsletters', 'vnewsNewsletters.id = vnewsReports.newsletter_id');
$c->leftJoin('vnewsSubscribers', 'vnewsSubscribers', 'vnewsSubscribers.id = vnewsReports.subscriber_id');
$c->select(array(
    'vnewsReports.*',
    'vnewsNewsletters.subject',
    'vnewsSubscribers.email',
    'vnewsSubscribers.name',
));

//date_default_timezone_set('UTC');
//$today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
$c->where(array(
    'newsletter_id' => $scriptProperties['newsletter_id'],
    'status' => 'queue',
//    'current_occurrence_time' => $today,
));

$limit = $modx->getOption('virtunewsletter.email_limit');
$c->limit($limit);

$queues = $modx->getCollection('vnewsReports', $c);
$outputReports = array();
if ($queues) {
    foreach ($queues as $queue) {
        $sent = $modx->virtunewsletter->sendNewsletter($scriptProperties['newsletter_id'], $queue->get('subscriber_id'));
        if ($sent) {
            $queue->set('status_logged_on', time());
            $queue->set('status', 'sent');
            if ($queue->save() === FALSE) {
                $modx->setDebug();
                $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                $modx->setDebug(FALSE);
            } else {
                $outputReports[] = $modx->virtunewsletter->getPlaceholders();
            }

            $nextOccurrenceTime = $queue->get('next_occurrence_time');
            if (!empty($nextOccurrenceTime)) {
                $currentOccurrenceTime = $nextOccurrenceTime;
                $nextOccurrenceTime = $modx->virtunewsletter->nextOccurrenceTime($queue->get('newsletter_id'), $nextOccurrenceTime);

                $report = $this->modx->newObject('vnewsReports');
                $params = array(
                    'subscriber_id' => $queue->get('subscriber_id'),
                    'newsletter_id' => $queue->get('newsletter_id'),
                    'current_occurrence_time' => $currentOccurrenceTime,
                    'status' => 'queue',
                    'status_logged_on' => time(),
                    'next_occurrence_time' => $nextOccurrenceTime,
                );
                $report->fromArray($params, NULL, TRUE);
                if ($report->save() === FALSE) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save report! ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(FALSE);
                } else {
                    $outputReports[] = $report->toArray();
                }
            }
        } else {
            $modx->setDebug();
            $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
            $modx->setDebug(FALSE);
        }
    }
}

return $this->success('', $outputReports);