<?php

/**
 * @license     public domain
 * @package     class helper methods
 */
class VirtuNewsletter {

    public $modx;
    public $config;

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
        $this->modx->addPackage('virtunewsletter', $this->config['modelPath'], 'modx_virtunewsletter_');
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

        $newsletters = $this->modx->getObject('vnewsNewsletters', $newsId);
        $newslettersArray = array();
        if ($newsletters) {
            $newslettersArray = $newsletters->toArray();
        }
        return $newslettersArray;
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
        $subscribers = $this->getNewsletterSubscribers($newsletter->get('id'));
        if (!$subscribers) {
            return FALSE;
        }
        $newsletterId = $newsletter->get('id');
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
                $this->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
                $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . ' ');
                $this->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': failed to save report! ' . print_r($params, TRUE));
                continue;
            } else {
                $outputReports[] = $report->toArray();
            }
        }

        return $outputReports;
    }

    /**
     * Set all queues
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @return  array   report's array
     */
    public function setQueues($todayOnly = TRUE) {
        $c = $this->modx->newQuery('vnewsNewsletters');
        $c->where(array(
            'content:!=' => '',
            'AND:is_active:=' => 1,
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
            $subscribers = $this->getNewsletterSubscribers($newsletterId);
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
                    $this->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
                    $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . ' ');
                    $this->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': failed to save report! ' . print_r($params, TRUE));
                } else {
                    $outputReports[] = $report->toArray();
                }
            }
        }

        return $outputReports;
    }

    /**
     * Get all subscribers for a newsletter
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|subscribers array
     */
    public function getNewsletterSubscribers($newsId) {
        $subscribersArray = array();

        $newsletter = $this->modx->getObject('vnewsNewsletters', $newsId);
        if (!$newsletter) {
            return FALSE;
        }

        $c = $this->modx->newQuery('vnewsNewslettersHasCategories');
        $c->distinct(TRUE);
        $newsletterHasCategories = $newsletter->getMany('vnewsNewslettersHasCategories', $c);
        if (!$newsletterHasCategories) {
            return FALSE;
        }

        $categories = array();
        foreach ($newsletterHasCategories as $newsHasCats) {
            $categories[] = $newsHasCats->getOne('vnewsCategories');
        }
        if (!$categories) {
            return FALSE;
        }
        foreach ($categories as $category) {
            // some newsletters might not have category at the moment
            if (empty($category)) {
                continue;
            }
            $subscribersHasCategories = $category->getMany('vnewsSubscribersHasCategories');
            if ($subscribersHasCategories) {
                foreach ($subscribersHasCategories as $subsHasCats) {
                    $subscribers = $subsHasCats->getOne('vnewsSubscribers');
                    if ($subscribers) {
                        $subscribersArray[] = $subscribers->toArray();
                    }
                }
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
                        $this->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
                        $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . ' ');
                        $this->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': failed to update a queue! ' . print_r($queue->toArray(), TRUE));
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
                    $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . ' ');
                    $this->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': failed to send a queue! ' . print_r($queue->toArray(), TRUE));
                }
            }
        }
    }

    public function sendNewsletter($newsId, $subscriberId) {

    }

}