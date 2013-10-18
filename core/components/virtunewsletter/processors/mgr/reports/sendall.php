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

date_default_timezone_set('UTC');
$c->where(array(
    'newsletter_id' => $scriptProperties['newsletter_id'],
    'status' => 'queue',
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
        } else {
            $modx->setDebug();
            $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
            $modx->setDebug(FALSE);
        }
    }
}

return $this->success('', $outputReports);