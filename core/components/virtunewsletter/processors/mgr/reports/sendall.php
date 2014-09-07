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
    'status' => 'queue',
));
$newsletter = $modx->getObject('vnewsNewsletters', $scriptProperties['newsletter_id']);
if (!$newsletter) {
    return $this->failure('Missing the newsletter');
}
$isRecurring = $newsletter->get('is_recurring');
if ($isRecurring) {
    $children = $modx->getCollection('vnewsNewsletters', array(
        'parent_id' => $scriptProperties['newsletter_id']
    ));
    $childrenIds = array();
    if ($children) {
        foreach ($children as $child) {
            $childrenIds[] = $child->get('id');
        }
    }
    if (!empty($childrenIds)) {
        $c->where(array(
            'newsletter_id:IN' => $childrenIds,
        ));
    }
} else {
    $c->where(array(
        'newsletter_id' => $scriptProperties['newsletter_id'],
    ));
}

$limit = $modx->getOption('virtunewsletter.email_limit');
$c->limit($limit);

$queues = $modx->getCollection('vnewsReports', $c);
$outputReports = array();
if ($queues) {
    $emailProvider = $modx->getOption('virtunewsletter.email_provider');
    if (!empty($emailProvider)) {
        $queuesArray = array();
        foreach ($queues as $queue) {
            $queuesArray[] = $queue->toArray();
        }
        $result = $modx->virtunewsletter->sendToEmailProvider($emailProvider, $scriptProperties['newsletter_id'], $queuesArray);
        if (!$result) {
            $error = $modx->virtunewsletter->getError();
            return $this->failure($error);
        } else {
            $output = $modx->virtunewsletter->getOutput();
            foreach ($output as $item) {
                if (isset($item['email']) && isset($item['status'])) {
                    $c = $modx->newQuery('vnewsReports');
                    $c->leftJoin('vnewsSubscribers', 'vnewsSubscribers', 'vnewsSubscribers.id = vnewsReports.subscriber_id');
                    $c->where(array(
                        'vnewsSubscribers.email' => $item['email']
                    ));
                    $itemQueue = $modx->getObject('vnewsReports', $c);
                    if ($itemQueue) {
                        $itemQueue->set('status_logged_on', time());
                        $itemQueue->set('status', $item['status']);
                        if ($itemQueue->save() === FALSE) {
                            $modx->setDebug();
                            $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                            $modx->setDebug(FALSE);
                        }
                    }
                }
            }
            $outputReports = $output;
        }
    } else {
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
}

return $this->success('', $outputReports);
