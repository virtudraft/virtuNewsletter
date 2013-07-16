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
 *
 * @package virtunewsletter
 * @subpackage main_class
 */
class VirtuNewsletter {

    const VERSION = '1.0.0-beta.5';

    /**
     * modX object
     * @var object
     */
    public $modx;

    /**
     * $scriptProperties
     * @var array
     */
    public $config;

    /**
     * To hold error message
     * @var string
     */
    private $_error = '';

    /**
     * To hold output message
     * @var string
     */
    private $_output = '';

    /**
     * To hold placeholder array, flatten array with prefixable
     * @var array
     */
    private $_placeholders = array();

    /**
     * constructor
     * @param   modX    $modx
     * @param   array   $config     parameters
     */
    public function __construct(modX $modx, $config = array()) {
        $this->modx = & $modx;
        $config = is_array($config) ? $config : array();
        $basePath = $this->modx->getOption('virtunewsletter.core_path', $config, $this->modx->getOption('core_path') . 'components/virtunewsletter/');
        $assetsUrl = $this->modx->getOption('virtunewsletter.assets_url', $config, $this->modx->getOption('assets_url') . 'components/virtunewsletter/');
        $this->config = array_merge(array(
            'version' => self::VERSION,
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath . 'model/',
            'processorsPath' => $basePath . 'processors/',
            'chunksPath' => $basePath . 'elements/chunks/',
            'templatesPath' => $basePath . 'templates/',
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl . 'conn/mgr.php',
            'webConnectorUrl' => $assetsUrl . 'conn/web.php',
                ), $config);

        $this->modx->lexicon->load('virtunewsletter:default');
        $this->modx->addPackage('virtunewsletter', $this->config['modelPath'], $modx->config[modX::OPT_TABLE_PREFIX] . 'virtunewsletter_');
    }

    /**
     * Set class configuration exclusively for multiple snippet calls
     * @param   array   $config     snippet's parameters
     */
    public function setConfigs(array $config = array()) {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Define individual config for the class
     * @param   string  $key    array's key
     * @param   string  $val    array's value
     */
    public function setConfig($key, $val) {
        $this->config[$key] = $val;
    }

    /**
     * Parsing template
     * @param   string  $tpl    @BINDINGs options
     * @param   array   $phs    placeholders
     * @return  string  parsed output
     * @link    http://forums.modx.com/thread/74071/help-with-getchunk-and-modx-speed-please?page=2#dis-post-413789
     */
    public function parseTpl($tpl, array $phs = array()) {
        $output = '';
        if (preg_match('/^(@CODE|@INLINE)/i', $tpl)) {
            $tplString = preg_replace('/^(@CODE|@INLINE)/i', '', $tpl);
            // tricks @CODE: / @INLINE:
            $tplString = ltrim($tplString, ':');
            $tplString = trim($tplString);
            $output = $this->parseTplCode($tplString, $phs);
        } elseif (preg_match('/^@FILE/i', $tpl)) {
            $tplFile = preg_replace('/^@FILE/i', '', $tpl);
            // tricks @FILE:
            $tplFile = ltrim($tplFile, ':');
            $tplFile = trim($tplFile);
            $tplFile = $this->replacePropPhs($tplFile);
            try {
                $output = $this->parseTplFile($tplFile, $phs);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        // ignore @CHUNK / @CHUNK: / empty @BINDING
        else {
            $tplChunk = preg_replace('/^@CHUNK/i', '', $tpl);
            // tricks @CHUNK:
            $tplChunk = ltrim($tpl, ':');
            $tplChunk = trim($tpl);

            $chunk = $this->modx->getObject('modChunk', array('name' => $tplChunk), true);
            if (empty($chunk)) {
                // try to use @splittingred's fallback
                $f = $this->config['chunksPath'] . strtolower($tplChunk) . '.chunk.tpl';
                try {
                    $output = $this->parseTplFile($f, $phs);
                } catch (Exception $e) {
                    $output = $e->getMessage();
                    return 'Chunk: ' . $tplChunk . ' is not found, neither the file ' . $output;
                }
            } else {
//                $output = $this->modx->getChunk($tpl, $phs);
                /**
                 * @link    http://forums.modx.com/thread/74071/help-with-getchunk-and-modx-speed-please?page=4#dis-post-464137
                 */
                $chunk = $this->modx->getParser()->getElement('modChunk', $tpl);
                $chunk->setCacheable(false);
                $chunk->_processed = false;
                $output = $chunk->process($phs);
            }
        }

        return $output;
    }

    /**
     * Parsing inline template code
     * @param   string  $code   HTML with tags
     * @param   array   $phs    placeholders
     * @return  string  parsed output
     */
    public function parseTplCode($code, array $phs = array()) {
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setContent($code);
        $chunk->setCacheable(false);
        $phs = $this->replacePropPhs($phs);
        $chunk->_processed = false;
        return $chunk->process($phs);
    }

    /**
     * Parsing file based template
     * @param   string  $file   file path
     * @param   array   $phs    placeholders
     * @return  string  parsed output
     * @throws  Exception if file is not found
     */
    public function parseTplFile($file, array $phs = array()) {
        if (!file_exists($file)) {
            throw new Exception('File: ' . $file . ' is not found.');
        }
        $o = file_get_contents($file);
        $chunk = $this->modx->newObject('modChunk');

        // just to create a name for the modChunk object.
        $name = strtolower(basename($file));
        $name = rtrim($name, '.tpl');
        $name = rtrim($name, '.chunk');
        $chunk->set('name', $name);

        $chunk->setCacheable(false);
        $chunk->setContent($o);
        $chunk->_processed = false;
        $output = $chunk->process($phs);

        return $output;
    }

    /**
     * If the chunk is called by AJAX processor, it needs to be parsed for the
     * other elements to work, like snippet and output filters.
     *
     * Example:
     * <pre><code>
     * <?php
     * $content = $myObject->parseTpl('tplName', $placeholders);
     * $content = $myObject->processElementTags($content);
     * </code></pre>
     *
     * @param   string  $content    the chunk output
     * @param   array   $options    option for iteration
     * @return  string  parsed content
     */
    public function processElementTags($content, array $options = array()) {
        $maxIterations = intval($this->modx->getOption('parser_max_iterations', $options, 10));
        if (!$this->modx->parser) {
            $this->modx->getParser();
        }
        $this->modx->parser->processElementTags('', $content, true, false, '[[', ']]', array(), $maxIterations);
        $this->modx->parser->processElementTags('', $content, true, true, '[[', ']]', array(), $maxIterations);
        return $content;
    }

    /**
     * Replace the property's placeholders
     * @param   string|array    $subject    Property
     * @return  array           The replaced results
     */
    public function replacePropPhs($subject) {
        $pattern = array(
            '/\{core_path\}/',
            '/\{base_path\}/',
            '/\{assets_url\}/',
            '/\{filemanager_path\}/',
            '/\[\[\+\+core_path\]\]/',
            '/\[\[\+\+base_path\]\]/'
        );
        $replacement = array(
            $this->modx->getOption('core_path'),
            $this->modx->getOption('base_path'),
            $this->modx->getOption('assets_url'),
            $this->modx->getOption('filemanager_path'),
            $this->modx->getOption('core_path'),
            $this->modx->getOption('base_path')
        );
        if (is_array($subject)) {
            $parsedString = array();
            foreach ($subject as $k => $s) {
                if (is_array($s)) {
                    $s = $this->replacePropPhs($s);
                }
                $parsedString[$k] = preg_replace($pattern, $replacement, $s);
            }
            return $parsedString;
        } else {
            return preg_replace($pattern, $replacement, $subject);
        }
    }

    /**
     * Get a newsletter
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|array
     */
    public function getNewsletter($newsId) {
        if (intval($newsId) < 1) {
            return FALSE;
        }

        $newsletter = $this->modx->getObject('vnewsNewsletters', $newsId);
        $newsletterArray = array();
        if ($newsletter) {
            $newsletterArray = $newsletter->toArray();
            if ($newsletterArray['is_recurring']) {
                $ctx = $this->modx->getObject('modResource', $newsletterArray['resource_id'])->get('context_key');
                $url = $this->modx->makeUrl($newsletterArray['resource_id'], $ctx, '', 'full');
                if (empty($url)) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get URL for newsletter w/ resource_id:' . $newsletterArray['resource_id'], '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(FALSE);
                    return FALSE;
                }
                $newsletterArray['content'] = file_get_contents($url);
            }
        }
        return $newsletterArray;
    }

    /**
     * Calculate the next occurrence time of a newsletter if it is recurring
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|null|UNIX date of the next occurrence
     */
    public function nextOccurrenceTime($newsId) {
        $newsletter = $this->modx->getObject('vnewsNewsletters', $newsId);
        if (!$newsletter) {
            return FALSE;
        }
        $currentOccurrenceTime = $newsletter->get('scheduled_for');
        $isRecurring = $newsletter->get('is_recurring');
        if (!$isRecurring) {
            $nextOccurrenceTime = NULL;
        } else {
            $timeRange = 0;
            $recurrenceRange = $newsletter->get('recurrence_range');
            $recurrenceNumber = $newsletter->get('recurrence_number');
            if (intval($recurrenceNumber) > 0) {
                if ($recurrenceRange === 'weekly') {
                    $daysRange = ceil(7 / $recurrenceNumber);
                    $timeRange = $daysRange * 24 * 60 * 60;
                } elseif ($recurrenceRange === 'monthly') {
                    $numberOfDays = date('t', $currentOccurrenceTime);
                    $daysRange = ceil($numberOfDays / $recurrenceNumber);
                    $timeRange = $daysRange * 24 * 60 * 60;
                } elseif ($recurrenceRange === 'yearly') {
                    $numberOfDays = date('z', mktime(0, 0, 0, 12, 31, date('Y', $currentOccurrenceTime))) + 1;
                    $daysRange = ceil($numberOfDays / $recurrenceNumber);
                    $timeRange = $daysRange * 24 * 60 * 60;
                }
            }

            $nextOccurrenceTime = $currentOccurrenceTime + $timeRange;
        }

        return $nextOccurrenceTime;
    }

    /**
     * Set subscribers queue of a newsletter
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|report array
     */
    public function setNewsletterQueue($newsId) {
        $newsletter = $this->modx->getObject('vnewsNewsletters', $newsId);
        if (!$newsletter) {
            return FALSE;
        }
        $time = time();

        $outputReports = array();
        $currentOccurrenceTime = $newsletter->get('scheduled_for');
        $nextOccurrenceTime = $this->nextOccurrenceTime($newsId);
        $subscribers = $this->newsletterHasSubscribers($newsId);
        if (!$subscribers) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get $subscribers w/ $newsId:' . $newsId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }
        $this->modx->removeCollection('vnewsReports', array(
            'newsletter_id' => $newsId,
        ));
        foreach ($subscribers as $subscriber) {
            $report = $this->modx->newObject('vnewsReports');
            $params = array(
                'subscriber_id' => $subscriber['id'],
                'newsletter_id' => $newsId,
                'current_occurrence_time' => $currentOccurrenceTime,
                'status' => 'queue',
                'status_logged_on' => $time,
                'next_occurrence_time' => $nextOccurrenceTime,
            );
            $report->fromArray($params, NULL, TRUE, TRUE);
            if ($report->save() === FALSE) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save report! ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(FALSE);
                continue;
            } else {
                $outputReports[] = $report->toArray();
            }
        }

        return $outputReports;
    }

    /**
     * Get all newsletters of a subscriber
     * @param   int     $subscriberId   subscriber's ID
     * @return  mixed   false|subscribers array
     */
    public function subscriberHasNewsletters($subscriberId) {
        $newslettersArray = array();

        $c = $this->modx->newQuery('vnewsNewsletters');
        $c->leftJoin('vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories.newsletter_id = vnewsNewsletters.id');
        $c->leftJoin('vnewsCategories', 'vnewsCategories', 'vnewsCategories.id = vnewsNewslettersHasCategories.category_id');
        $c->leftJoin('vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories.category_id = vnewsCategories.id');
        $c->leftJoin('vnewsSubscribers', 'vnewsSubscribers', 'vnewsSubscribers.id = vnewsSubscribersHasCategories.subscriber_id');
        $c->where(array(
            'vnewsSubscribers.id' => $subscriberId
        ));

        $newsletters = $this->modx->getCollection('vnewsNewsletters', $c);
        if ($newsletters) {
            foreach ($newsletters as $newsletter) {
                $newslettersArray[] = $newsletter->toArray();
            }
        }

        return $newslettersArray;
    }

    /**
     * Add this user to the newletters' queues
     * @param   int     $subscriberId   subscriber's ID
     * @return  boolean
     */
    public function addSubscriberQueues($subscriberId) {
        $newsletters = $this->subscriberHasNewsletters($subscriberId);
        if (!$newsletters) {
            return FALSE;
        }
        foreach ($newsletters as $item) {
            $newsletter = $this->modx->getObject('vnewsNewsletters', $item['id']);
            if (!$newsletter) {
                continue;
            }
            $newsId = $newsletter->get('id');
            $currentOccurrenceTime = $newsletter->get('scheduled_for');
            $nextOccurrenceTime = $this->nextOccurrenceTime($newsId);
            $params = array(
                'newsletter_id' => $newsId,
                'subscriber_id' => $subscriberId,
                'current_occurrence_time' => $currentOccurrenceTime,
                'status' => 'queue',
                'status_logged_on' => time(),
                'next_occurrence_time' => $nextOccurrenceTime,
            );
            $newReport = $this->modx->newObject('vnewsReports');
            $newReport->fromArray($params, NULL, TRUE, TRUE);
            $newReport->save();
        }
        return TRUE;
    }

    /**
     * Remove this user from all newletters' queues
     * @param   int     $subscriberId   subscriber's ID
     * @return  boolean
     */
    public function removeSubscriberQueues($subscriberId) {
        return $this->modx->removeCollection('vnewsReports', array(
                    'subscriber_id' => $subscriberId
        ));
    }

    /**
     * Set all queues
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @return  array   report's array
     */
    public function setQueues($todayOnly = TRUE) {
        $c = $this->modx->newQuery('vnewsNewsletters');
        $c->where(array(
            'is_active' => 1,
        ));
        if ($todayOnly) {
            date_default_timezone_set('UTC');
            $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
            $c->where(array(
                'scheduled_for' => $today,
                    ), 'AND');
        }
        $newsletters = $this->modx->getCollection('vnewsNewsletters', $c);
        $time = time();

        $outputReports = array();
        foreach ($newsletters as $newsletter) {
            $currentOccurrenceTime = $newsletter->get('scheduled_for');
            $newsletterId = $newsletter->get('id');
            $nextOccurrenceTime = $this->nextOccurrenceTime($newsletterId);
            $subscribers = $this->newsletterHasSubscribers($newsletterId);
            if (!$subscribers) {
                continue;
            }
            foreach ($subscribers as $subscriber) {
                $report = $this->modx->getObject('vnewsReports', array(
                    'subscriber_id' => $subscriber['id'],
                    'newsletter_id' => $newsletterId,
                    'current_occurrence_time' => $currentOccurrenceTime,
                ));
                if ($report) {
                    continue;
                }

                $report = $this->modx->newObject('vnewsReports');
                $params = array(
                    'subscriber_id' => $subscriber['id'],
                    'newsletter_id' => $newsletterId,
                    'current_occurrence_time' => $currentOccurrenceTime,
                    'status' => 'queue',
                    'status_logged_on' => $time,
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
        }

        return $outputReports;
    }

    /**
     * Get all subscribers of a newsletter
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|subscribers array
     */
    public function newsletterHasSubscribers($newsId) {
        $subscribersArray = array();

        $c = $this->modx->newQuery('vnewsSubscribers');
        $c->leftJoin('vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories.subscriber_id = vnewsSubscribers.id');
        $c->leftJoin('vnewsCategories', 'vnewsCategories', 'vnewsCategories.id = vnewsSubscribersHasCategories.category_id');
        $c->leftJoin('vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories.category_id = vnewsCategories.id');
        $c->leftJoin('vnewsNewsletters', 'vnewsNewsletters', 'vnewsNewsletters.id = vnewsNewslettersHasCategories.newsletter_id');
        $c->where(array(
            'vnewsSubscribers.is_active' => 1,
            'vnewsNewsletters.id' => $newsId
        ));

        $subscribers = $this->modx->getCollection('vnewsSubscribers', $c);
        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $subscribersArray[] = $subscriber->toArray();
            }
        }

        return $subscribersArray;
    }

    /**
     * Process queue
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @return void
     */
    public function processQueue($todayOnly = FALSE) {
        $c = $this->modx->newQuery('vnewsReports');
        $c->where(array(
            'status' => 'queue'
        ));
        if ($todayOnly) {
            date_default_timezone_set('UTC');
            $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
            $c->where(array(
                'current_occurrence_time' => $today,
            ));
        }
        $limit = $this->modx->getOption('virtunewsletter.email_limit');
        $c->limit($limit);
        $queues = $this->modx->getCollection('vnewsReports', $c);
        if ($queues) {
            foreach ($queues as $queue) {
                $sent = $this->sendNewsletter($queue->get('newsletter_id'), $queue->get('subscriber_id'));
                if ($sent) {
                    $queue->set('status_logged_on', time());
                    $nextOccurrenceTime = $queue->get('next_occurrence_time');
                    if (!empty($nextOccurrenceTime)) {
                        $queue->set('current_occurrence_time', $nextOccurrenceTime);
                        $nextOccurrenceTime = $this->nextOccurrenceTime($queue->get('newsletter_id'));
                        $queue->set('next_occurrence_time', $nextOccurrenceTime);
                    } else {
                        $queue->set('status', 'sent');
                    }
                    if ($queue->save() === FALSE) {
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(FALSE);
                    }
                } else {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(FALSE);
                }
            }
        }
    }

    /**
     * Subscribe a user
     * @param   array   $fields required information (email) and additional one (name)
     * @return  boolean
     */
    public function subscribe($fields) {
        if (!isset($fields[$this->config['emailKey']])) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_exists', array(
                'email' => $fields[$this->config['emailKey']]
            ));
            $this->setError($msg);
            return FALSE;
        }
        $registeredSubscriber = $this->modx->getObject('vnewsSubscribers', array(
            'email' => $fields[$this->config['emailKey']]
        ));
        if ($registeredSubscriber) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_exists', array(
                'email' => $fields[$this->config['emailKey']]
            ));
            $this->setError($msg);
            return FALSE;
//            $name = $registeredSubscriber->get('name');
//            if (isset($fields[$this->config['nameKey']])) {
//                $name = $fields[$this->config['nameKey']];
//            }
//            $registeredSubscriber->set('name', $name);
//            $registeredSubscriber->set('is_active', 1);
//            return TRUE;
        }
        $newSubscriber = $this->modx->newObject('vnewsSubscribers');

        $c = $this->modx->newQuery('vnewsUsers');
        $c->leftJoin('modUserProfile', 'modUserProfile', 'vnewsUsers.id = modUserProfile.internalKey');
        $c->where(array(
            'modUserProfile.email' => $fields[$this->config['emailKey']]
        ));
        $user = $this->modx->getObject('vnewsUsers', $c);
        $name = '';
        if (isset($fields[$this->config['nameKey']])) {
            $name = $fields[$this->config['nameKey']];
        }
        $userId = '';
        if ($user) {
            if (empty($name)) {
                $fullname = $user->getOne('Profile')->get('fullname');
                $name = !empty($fullname) ? $fullname : $user->get('username');
            }
            $userId = $user->get('id');
        }

        $params = array(
            'user_id' => $userId,
            'email' => $fields[$this->config['emailKey']],
            'name' => $name,
            'is_active' => 0, // wait to confirm
            'hash' => $this->setHash($fields[$this->config['emailKey']])
        );

        $newSubscriber->fromArray($params);
        if ($newSubscriber->save() === FALSE) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save a new subscriber! ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_err_save');
            $this->setError($msg);
            return FALSE;
        }

        /**
         * If defined in the input field, set user to the categories
         */
        if (isset($fields[$this->config['categoryKey']])) {
            $category = $this->modx->getObjectGraph('vnewsCategories'
                    , array('vnewsSubscribersHasCategories' => array())
                    , array(
                'name' => $fields[$this->config['categoryKey']]
            ));
            if ($category) {
                $subscribersHasCategories = $this->modx->newObject('vnewsSubscribersHasCategories');
                $addManyParams = array(
                    'subscriber_id' => $newSubscriber->getPrimaryKey(),
                    'category_id' => $category->get('id')
                );
                $subscribersHasCategories->fromArray($addManyParams, '', TRUE, TRUE);
                $addMany = array($subscribersHasCategories);
                $newSubscriber->addMany($addMany);
                $newSubscriber->save();
            }
        }

        $msg = $this->modx->lexicon('virtunewsletter.subscriber_suc_save');
        $this->setOutput($msg);

        $phs = $newSubscriber->toArray();
        $phs = array_merge($phs, array('act' => 'subscribe'));
        $this->setPlaceholders($phs);

        return TRUE;
    }

    /**
     * Get subsriber
     * @param   array   $where  criteria in an array
     * @return  mixed   false|array of arguments
     */
    public function getSubscriber(array $where = array()) {
        $c = $this->modx->newQuery('vnewsSubscribers');
        if (!empty($where)) {
            $c->where($where);
        }
        $subscriber = $this->modx->getObject('vnewsSubscribers', $c);
        if (!$subscriber) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unabled to get subscriber with criteria: ' . print_r($where, TRUE), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }
        $linkArgs = array(
            'subid' => $subscriber->get('id'),
            'hash' => $subscriber->get('hash'),
            'email' => $subscriber->get('email'),
        );
        return $linkArgs;
    }

    /**
     * Confirmation action
     * @param   array   $arguments  required arguments
     * @return  boolean
     */
    public function confirmAction($arguments) {
        $subscriber = $this->modx->getObject('vnewsSubscribers', array(
            'id' => $arguments['id'],
            'hash' => $arguments['hash'],
        ));
        if (!$subscriber) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_err_ne');
            $this->setError($msg);
            return FALSE;
        }
        $this->setPlaceholder('emailTo', $subscriber->get('email'));

        switch ($arguments['action']) {
            case 'subscribe':
                $subscriber->set('is_active', 1);
                $subscriber->save();
                $this->addSubscriberQueues($subscriber->get('id'));
                $msg = $this->modx->lexicon('virtunewsletter.subscriber_suc_activated');
                $this->setOutput($msg);
                break;
            case 'unsubscribe':
                $subscriber->set('is_active', 0);
                $subscriber->save();
                $this->removeSubscriberQueues($subscriber->get('id'));
                $msg = $this->modx->lexicon('virtunewsletter.subscriber_suc_deactivated');
                $this->setOutput($msg);
                break;
            default:
                break;
        }

        return TRUE;
    }

    /**
     * Hash for email
     * @param   string  $email  email address
     * @return  string  hashed string
     */
    public function setHash($email) {
        return str_rot13(base64_encode(hash('sha512', time() . $email)));
    }

    /**
     * Set string error for boolean returned methods
     * @return  void
     */
    public function setError($msg) {
        $this->_error = $msg;
    }

    /**
     * Get string error for boolean returned methods
     * @return  string  output
     */
    public function getError() {
        return $this->_error;
    }

    /**
     * Set string output for boolean returned methods
     * @return  void
     */
    public function setOutput($msg) {
        $this->_output = $msg;
    }

    /**
     * Get string output for boolean returned methods
     * @return  string  output
     */
    public function getOutput() {
        return $this->_output;
    }

    /**
     * Trim array values
     * @param   array   $array          array contents
     * @param   string  $charlist       [default: null] defined characters to be trimmed
     * @link http://php.net/manual/en/function.trim.php
     * @return  array   trimmed array
     */
    public function trimArray($input, $charlist = null) {
        if (is_array($input)) {
            $output = array_map(array($this, 'trimArray'), $input);
        } else {
            $output = $this->trimString($input, $charlist);
        }

        return $output;
    }

    /**
     * Trim string value
     * @param   string  $string     source text
     * @param   string  $charlist   defined characters to be trimmed
     * @link http://php.net/manual/en/function.trim.php
     * @return  string  trimmed text
     */
    public function trimString($string, $charlist = null) {
        if (empty($string)) {
            return '';
        }
        $string = htmlentities($string);
        // blame TinyMCE!
        $string = preg_replace('/(&Acirc;|&nbsp;)+/i', '', $string);
        $string = trim($string, $charlist);
        $string = trim(preg_replace('/\s+^(\r|\n|\r\n)/', ' ', $string));
        $string = html_entity_decode($string);
        return $string;
    }

    /**
     * Merge multi dimensional associative arrays with separator
     * @param   array   $array      raw associative array
     * @param   string  $keyName    parent key of this array
     * @param   string  $separator  separator between the merged keys
     * @param   array   $holder     to hold temporary array results
     * @return  array   one level array
     */
    public function implodePhs(array $array, $keyName = null, $separator = '.', array $holder = array()) {
        $phs = !empty($holder) ? $holder : array();
        foreach ($array as $k => $v) {
            $key = !empty($keyName) ? $keyName . $separator . $k : $k;
            if (is_array($v)) {
                $phs = $this->implodePhs($v, $key, $separator, $phs);
            } else {
                $phs[$key] = $v;
            }
        }
        return $phs;
    }

    /**
     * Set internal placeholder
     * @param   string  $key    key
     * @param   string  $value  value
     * @param   string  $prefix add prefix if it's required
     */
    public function setPlaceholder($key, $value, $prefix = '') {
        $prefix = !empty($prefix) ? $prefix : (isset($this->config['phsPrefix']) ? $this->config['phsPrefix'] : '');
        $this->_placeholders[$prefix . $key] = $value;
    }

    /**
     * Set internal placeholders
     * @param   array   $placeholders   placeholders in an associative array
     * @param   string  $prefix         add prefix if it's required
     * @return  mixed   boolean|array of placeholders
     */
    public function setPlaceholders($placeholders, $prefix = '') {
        if (empty($placeholders)) {
            return FALSE;
        }
        $prefix = !empty($prefix) ? $prefix : (isset($this->config['phsPrefix']) ? $this->config['phsPrefix'] : '');
        $placeholders = $this->trimArray($placeholders);
        $placeholders = $this->implodePhs($placeholders, rtrim($prefix, '.'));
        // enclosed private scope
        $this->_placeholders = array_merge($this->_placeholders, $placeholders);
        // return only for this scope
        return $placeholders;
    }

    /**
     * Get internal placeholders in an associative array
     * @return array
     */
    public function getPlaceholders() {
        return $this->_placeholders;
    }

    /**
     * Get an internal placeholder
     * @param   string  $key    key
     * @return  string  value
     */
    public function getPlaceholder($key) {
        return $this->_placeholders[$key];
    }

    /**
     * Apply CSS rules:
     * @param   string  $html   HTML content
     *
     * @return  string  parsed HTML content
     * @author  Josh Gulledge <jgulledge19@hotmail.com>
     */
    public function inlineCss($html) {
        if (empty($html)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'empty $html to create inline CSS', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }
        require_once MODX_CORE_PATH . 'components/virtunewsletter/vendors/CssToInlineStyles/Exception.php';
        require_once MODX_CORE_PATH . 'components/virtunewsletter/vendors/CssToInlineStyles/CssToInlineStyles.php';

        // embedded CSS:
        preg_match_all('|<style(.*)>(.*)</style>|isU', $html, $css);
        $css_rules = '';
        if (!empty($css[2])) {
            foreach ($css[2] as $cssblock) {
                $css_rules .= $cssblock;
            }
        }

        $cssToInlineStyles = new TijsVerkoyen\CSSToInlineStyles\CSSToInlineStyles($html, $css_rules);

        // the processed HTML
        $html = $cssToInlineStyles->convert();

        // remove tags: http://www.php.net/manual/en/domdocument.savehtml.php#85165
        $html = preg_replace('/^<!DOCTYPE.+? >/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $html) );

        return $html;
    }

    /**
     * Send newsletter emails
     * @param   int $newsId         Newsletter's ID
     * @param   int $subscriberId   Subscriber's ID
     * @return  boolean
     */
    public function sendNewsletter($newsId, $subscriberId) {
        $newsletter = $this->modx->getObject('vnewsNewsletters', $newsId);
        if (!$newsletter) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:' . $newsId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }
        $newsletterArray = $newsletter->toArray();
        $subscriber = $this->modx->getObject('vnewsSubscribers', $subscriberId);
        if (!$subscriber) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get subscriber w/ id:' . $subscriberId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }
        $subscriberArray = $subscriber->toArray();
        if ($newsletterArray['is_recurring']) {
            $ctx = $this->modx->getObject('modResource', $newsletterArray['resource_id'])->get('context_key');
            $url = $this->modx->makeUrl($newsletterArray['resource_id'], $ctx, '', 'full');
            if (empty($url)) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get URL for newsletter w/ resource_id:' . $newsletterArray['resource_id'], '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(FALSE);
                return FALSE;
            }
            $newsletterArray['content'] = file_get_contents($url);
        }

        $confirmLinkArgs = $this->getSubscriber(array('email' => $subscriberArray['email']));
        $confirmLinkArgs = array_merge($confirmLinkArgs, array('act' => 'unsubscribe'));
        $phs = array_merge($subscriberArray, $confirmLinkArgs, array('id' => $newsId));
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $this->setPlaceholders($phs, $systemEmailPrefix);
        $phs = $this->getPlaceholders();
        return $this->sendMail($newsletterArray['subject'], $newsletterArray['content'], $subscriberArray['email'], $phs);
    }

    /**
     * Send email
     * @param   string  $subject        Email's subject
     * @param   string  $message        Email's body
     * @param   string  $emailTo        Email address of the receiver
     * @param   array   $phs            placeholders for the email's body
     * @param   string  $emailFrom      Email address of the sender
     * @param   string  $emailFromName  Name of the sender
     * @return boolean
     */
    public function sendMail($subject, $message, $emailTo, $phs = array(), $emailFrom = '', $emailFromName = '') {
        if (!$this->modx->mail) {
            if (!$this->modx->getService('mail', 'mail.modPHPMailer')) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to load modPHPMailer class!', '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(FALSE);
                return FALSE;
            }
        }
        if (empty($emailTo)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing email target!', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }

        if (empty($emailFrom)) {
            $emailFrom = $this->modx->getOption('virtunewsletter.email_sender');
            if (empty($emailFrom)) {
                $emailFrom = $this->modx->getOption('emailsender');
                if (empty($emailFrom)) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing email sender!', '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(FALSE);
                    return FALSE;
                }
            }
        }
        if (empty($emailFromName)) {
            $emailFromName = $this->modx->getOption('site_name');
        }
        $message = str_replace('%5B%5B%2B', '[[+', $message);
        $message = str_replace('%5D%5D', ']]', $message);
        $message = $this->parseTplCode($message, $phs);
        $message = $this->processElementTags($message);
        $message = $this->inlineCss($message);
        if ($this->modx->getOption('virtunewsletter.email_debug')) {
            $this->modx->setDebug();
            $debugOutput = array(
                'subject' => $subject,
                'message' => $message,
                'emailTo' => $emailTo,
                'emailFrom' => $emailFrom,
                'emailFromName' => $emailFromName,
                'placeholders' => $phs,
            );
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($debugOutput, TRUE), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return TRUE;
        }

        $this->modx->mail->set(modMail::MAIL_BODY, $message);
        $this->modx->mail->set(modMail::MAIL_FROM, $emailFrom);
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $emailFromName);
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);
        $this->modx->mail->address('to', $emailTo);
        $this->modx->mail->address('reply-to', $emailFrom);
        $this->modx->mail->setHTML(true);
        if (!$this->modx->mail->send()) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $this->modx->mail->mailer->ErrorInfo, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return FALSE;
        }
        $this->modx->mail->reset();

        return TRUE;
    }

}