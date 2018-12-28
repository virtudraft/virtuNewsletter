<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2016 by goldsky <goldsky@virtudraft.com>
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
class VirtuNewsletter
{

    const VERSION = '2.4.1';
    const RELEASE = 'pl';

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
     * store the chunk's HTML to property to save memory of loop rendering
     * @var array
     */
    private $_chunks = array();

    /**
     * store responses from email provider
     * @var array
     */
    private $_responses = array();

    /**
     * constructor
     * @param   modX    $modx
     * @param   array   $config     parameters
     */
    public function __construct(modX $modx, $config = array())
    {
        $this->modx   = & $modx;
        $config       = is_array($config) ? $config : array();
        $basePath     = $this->modx->getOption('virtunewsletter.core_path', $config, $this->modx->getOption('core_path').'components/virtunewsletter/');
        $assetsUrl    = $this->modx->getOption('virtunewsletter.assets_url', $config, $this->modx->getOption('assets_url').'components/virtunewsletter/');
        $this->config = array_merge(array(
            'version'         => self::VERSION.'-'.self::RELEASE,
            'basePath'        => $basePath,
            'corePath'        => $basePath,
            'modelPath'       => $basePath.'model/',
            'processorsPath'  => $basePath.'processors/',
            'chunksPath'      => $basePath.'elements/chunks/',
            'templatesPath'   => $basePath.'templates/',
            'jsUrl'           => $assetsUrl.'js/',
            'cssUrl'          => $assetsUrl.'css/',
            'assetsUrl'       => $assetsUrl,
            'connectorUrl'    => $assetsUrl.'conn/mgr.php',
            'webConnectorUrl' => $assetsUrl.'conn/web.php',
            ), $config);

        $this->modx->lexicon->load('virtunewsletter:default');
        $tablePrefix = $this->modx->getOption('virtunewsletter.table_prefix', null, $this->modx->config[modX::OPT_TABLE_PREFIX].'virtunewsletter_');
        $this->modx->addPackage('virtunewsletter', $this->config['modelPath'], $tablePrefix);
    }

    /**
     * Set class configuration exclusively for multiple snippet calls
     * @param   array   $config     snippet's parameters
     */
    public function setConfigs(array $config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Define individual config for the class
     * @param   string  $key    array's key
     * @param   string  $val    array's value
     */
    public function setConfig($key, $val)
    {
        $this->config[$key] = $val;
    }

    /**
     * Get individual config for the class
     * @param   string  $key        array's key
     * @param   string  $default    default value
     */
    public function getConfig($key, $default = null)
    {
        if (!isset($this->config[$key])) {
            return $default;
        }
        return $this->config[$key];
    }

    /**
     * Set string error for boolean returned methods
     * @return  void
     */
    public function setError($msg)
    {
        $this->_error = $msg;
    }

    /**
     * Get string error for boolean returned methods
     * @return  string  output
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Set string output for boolean returned methods
     * @return  void
     */
    public function setOutput($msg)
    {
        $this->_output = $msg;
    }

    /**
     * Get string output for boolean returned methods
     * @return  string  output
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * Add array response from email provider
     * @return  void
     */
    public function addResponse(array $response = array())
    {
        $this->_responses = array_merge($this->_responses, $response);
    }

    /**
     * Get array output for the response of email provider
     * @return  array  responses
     */
    public function getResponses()
    {
        return $this->_responses;
    }

    /**
     * Reset the response of email provider
     * @return  void
     */
    public function resetResponses()
    {
        $this->_responses = array();
    }

    /**
     * Set internal placeholder
     * @param   string  $key    key
     * @param   string  $value  value
     * @param   string  $prefix add prefix if it's required
     */
    public function setPlaceholder($key, $value, $prefix = '')
    {
        $prefix                            = !empty($prefix) ? $prefix : (isset($this->config['phsPrefix']) ? $this->config['phsPrefix'] : '');
        $this->_placeholders[$prefix.$key] = $this->trimString($value);
    }

    /**
     * Get an internal placeholder
     * @param   string  $key    key
     * @return  string  value
     */
    public function getPlaceholder($key)
    {
        return $this->_placeholders[$key];
    }

    /**
     * Set internal placeholders
     * @param   array   $placeholders   placeholders in an associative array
     * @param   string  $prefix         add prefix if it's required
     * @param   boolean $merge          define whether the output will be merge to global properties or not
     * @param   string  $delimiter      define placeholder's delimiter
     * @return  mixed   boolean|array of placeholders
     */
    public function setPlaceholders($placeholders, $prefix = '', $merge = true, $delimiter = '.')
    {
        if (empty($placeholders)) {
            return false;
        }
        $prefix       = !empty($prefix) ? $prefix : (isset($this->config['phsPrefix']) ? $this->config['phsPrefix'] : '');
        $placeholders = $this->trimArray($placeholders);
        $placeholders = $this->implodePhs($placeholders, rtrim($prefix, $delimiter));
        // enclosed private scope
        if ($merge) {
            $this->_placeholders = array_merge($this->_placeholders, $placeholders);
        }
        // return only for this scope
        return $placeholders;
    }

    /**
     * Get internal placeholders in an associative array
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->_placeholders;
    }

    /**
     * Merge multi dimensional associative arrays with separator
     * @param   array   $array      raw associative array
     * @param   string  $keyName    parent key of this array
     * @param   string  $separator  separator between the merged keys
     * @param   array   $holder     to hold temporary array results
     * @return  array   one level array
     */
    public function implodePhs(array $array, $keyName = null, $separator = '.', array $holder = array())
    {
        $phs = !empty($holder) ? $holder : array();
        foreach ($array as $k => $v) {
            $key = !empty($keyName) ? $keyName.$separator.$k : $k;
            if (is_array($v)) {
                $phs = $this->implodePhs($v, $key, $separator, $phs);
            } else {
                $phs[$key] = $v;
            }
        }
        return $phs;
    }

    /**
     * Trim string value
     * @param   string  $string     source text
     * @param   string  $charlist   defined characters to be trimmed
     * @link http://php.net/manual/en/function.trim.php
     * @return  string  trimmed text
     */
    public function trimString($string, $charlist = null)
    {
        if (empty($string) && !is_numeric($string)) {
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
     * Trim array values
     * @param   array   $array          array contents
     * @param   string  $charlist       [default: null] defined characters to be trimmed
     * @link http://php.net/manual/en/function.trim.php
     * @return  array   trimmed array
     */
    public function trimArray($input, $charlist = null)
    {
        if (is_array($input)) {
            $output = array_map(array($this, 'trimArray'), $input);
        } else {
            $output = $this->trimString($input, $charlist);
        }

        return $output;
    }

    /**
     * Parsing template
     * @param   string  $tpl    @BINDINGs options
     * @param   array   $phs    placeholders
     * @return  string  parsed output
     * @link    http://forums.modx.com/thread/74071/help-with-getchunk-and-modx-speed-please?page=2#dis-post-413789
     */
    public function parseTpl($tpl, array $phs = array())
    {
        $output = '';

        if (isset($this->_chunks[$tpl]) && !empty($this->_chunks[$tpl])) {
            return $this->parseTplCode($this->_chunks[$tpl], $phs);
        }

        if (preg_match('/^(@CODE|@INLINE)/i', $tpl)) {
            $tplString           = preg_replace('/^(@CODE|@INLINE)/i', '', $tpl);
            // tricks @CODE: / @INLINE:
            $tplString           = ltrim($tplString, ':');
            $tplString           = trim($tplString);
            $this->_chunks[$tpl] = $tplString;
            $output              = $this->parseTplCode($tplString, $phs);
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
                $f = $this->config['chunksPath'].strtolower($tplChunk).'.chunk.tpl';
                try {
                    $output = $this->parseTplFile($f, $phs);
                } catch (Exception $e) {
                    $output = $e->getMessage();
                    return 'Chunk: '.$tplChunk.' is not found, neither the file '.$output;
                }
            } else {
//                $output = $this->modx->getChunk($tplChunk, $phs);
                /**
                 * @link    http://forums.modx.com/thread/74071/help-with-getchunk-and-modx-speed-please?page=4#dis-post-464137
                 */
                $chunk               = $this->modx->getParser()->getElement('modChunk', $tplChunk);
                $this->_chunks[$tpl] = $chunk->get('content');
                $chunk->setCacheable(false);
                $chunk->_processed   = false;
                $output              = $chunk->process($phs);
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
    public function parseTplCode($code, array $phs = array())
    {
        $chunk             = $this->modx->newObject('modChunk');
        $chunk->setContent($code);
        $chunk->setCacheable(false);
        $phs               = $this->replacePropPhs($phs);
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
    public function parseTplFile($file, array $phs = array())
    {
        if (!file_exists($file)) {
            throw new Exception('File: '.$file.' is not found.');
        }
        $o                    = file_get_contents($file);
        $this->_chunks[$file] = $o;
        $chunk                = $this->modx->newObject('modChunk');

        // just to create a name for the modChunk object.
        $name = strtolower(basename($file));
        $name = rtrim($name, '.tpl');
        $name = rtrim($name, '.chunk');
        $chunk->set('name', $name);

        $chunk->setCacheable(false);
        $chunk->setContent($o);
        $chunk->_processed = false;
        $output            = $chunk->process($phs);

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
    public function processElementTags($content, array $options = array())
    {
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
    public function replacePropPhs($subject)
    {
        $pattern     = array(
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
     * Get a newsletter, process recurring one as well
     * @param   int     $newsId newsletter's ID
     * @param   array   $where  optional conditional in array
     * @return  mixed   false|array
     */
    public function getNewsletter($newsId, array $where = array())
    {
        if (intval($newsId) < 1) {
            return false;
        }
        $c = $this->modx->newQuery('vnewsNewsletters');
        $c->where(array(
            'id' => $newsId
        ));
        if (!empty($where)) {
            $c->where($where);
        }
        $newsletter      = $this->modx->getObject('vnewsNewsletters', $c);
        $newsletterArray = array();
        if ($newsletter) {
            $newsletterArray = $newsletter->toArray();
            if ($newsletterArray['is_recurring']) {
                $recurringNewsletter = $this->createNextRecurrence($newsletterArray['id']);
                if (empty($recurringNewsletter)) {
                    return false;
                }
                $newsletterArray = $recurringNewsletter;
            }
        }
        return $newsletterArray;
    }

    /**
     * Calculate the next recurrence time of a recurring newsletter
     * @param   int     $newsId                 newsletter's ID
     * @param   int     $currentRecurrenceTime  optional current recurence time to override
     * @return  mixed   false|null|UNIX date of the next occurrence
     */
    public function nextRecurrenceTime($newsId, $currentRecurrenceTime = '')
    {
        $newsletter = $this->modx->getObject('vnewsNewsletters', $newsId);
        if (!$newsletter) {
            return false;
        }
        if (empty($currentRecurrenceTime)) {
            $currentRecurrenceTime = $newsletter->get('scheduled_for');
        }
        if ($currentRecurrenceTime > time()) {
            return $currentRecurrenceTime;
        }
        $isRecurring = $newsletter->get('is_recurring');
        if (!$isRecurring) {
            $nextRecurrenceTime = NULL;
        } else {
            $timeRange        = 0;
            $recurrenceRange  = $newsletter->get('recurrence_range');
            $recurrenceNumber = $newsletter->get('recurrence_number');
            if (intval($recurrenceNumber) > 0) {
                if ($recurrenceRange === 'weekly') {
                    $daysRange = ceil(7 / $recurrenceNumber);
                    $timeRange = $daysRange * 24 * 60 * 60;
                } elseif ($recurrenceRange === 'monthly') {
                    $numberOfDays = date('t', $currentRecurrenceTime);
                    $daysRange    = ceil($numberOfDays / $recurrenceNumber);
                    $timeRange    = $daysRange * 24 * 60 * 60;
                } elseif ($recurrenceRange === 'yearly') {
                    $numberOfDays = date('z', mktime(0, 0, 0, 12, 31, date('Y', $currentRecurrenceTime))) + 1;
                    $daysRange    = ceil($numberOfDays / $recurrenceNumber);
                    $timeRange    = $daysRange * 24 * 60 * 60;
                }
            }

            $nextRecurrenceTime = $currentRecurrenceTime + $timeRange;
        }
        if ($nextRecurrenceTime < time()) {
            return $this->nextRecurrenceTime($newsId, $nextRecurrenceTime);
        }
        return $nextRecurrenceTime;
    }

    /**
     * Set subscribers queue of a newsletter
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|report array
     */
    public function setNewsletterQueue($newsId)
    {
        $newsletterArray = $this->getNewsletter($newsId);
        if (empty($newsletterArray)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:'.$newsId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        $time = time();

        $outputReports = array();
        $subscribers   = $this->modx->getObject('vnewsNewsletters', $newsletterArray['id'])->getSubscribers();
        if (!$subscribers) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get $subscribers w/ $newsId:'.$newsletterArray['id'], '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }

        foreach ($subscribers as $subscriber) {
            $report = $this->modx->getObject('vnewsReports', array(
                'subscriber_id' => $subscriber['id'],
                'newsletter_id' => $newsletterArray['id'],
            ));
            if (!empty($report)) {
                continue;
            }
            $report = $this->modx->newObject('vnewsReports');
            $params = array(
                'subscriber_id'    => $subscriber['id'],
                'newsletter_id'    => $newsletterArray['id'],
                'status'           => 'queue',
                'status_logged_on' => $time,
            );
            $report->fromArray($params);
            if ($report->save() === false) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save report! '.print_r($params, true), '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
                continue;
            } else {
                $outputReports[] = $report->toArray();
            }
        }

        return $outputReports;
    }

    /**
     * @deprecated since version 1.6.0-beta2
     * Get all newsletters of a subscriber
     * @param   int     $subscriberId   subscriber's ID
     * @return  mixed   false|subscribers array
     */
    public function subscriberHasNewsletters($subscriberId)
    {
        $newslettersArray = array();

        $c = $this->modx->newQuery('vnewsNewsletters');
        $c->leftJoin('vnewsNewslettersHasCategories', 'NewslettersHasCategories', 'NewslettersHasCategories.newsletter_id = vnewsNewsletters.id');
        $c->leftJoin('vnewsCategories', 'Categories', 'Categories.id = NewslettersHasCategories.category_id');
        $c->leftJoin('vnewsSubscribersHasCategories', 'SubscribersHasCategories', 'SubscribersHasCategories.category_id = Categories.id');
        $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = SubscribersHasCategories.subscriber_id');
        $c->where(array(
            'Subscribers.id' => $subscriberId
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
     * @param   int     $subscriberId       subscriber's ID
     * @param   boolean $reQueueExisting    if exists, re-queue subscriber or not?
     * @return  boolean
     */
    public function addSubscriberQueues($subscriberId, $reQueueExisting = true)
    {
        $newsletters = $this->modx->getObject('vnewsSubscribers', $subscriberId)->getNewsletters(array(
            'upcomingOnly' => true
        ));
        if (!$newsletters) {
            return false;
        }
        foreach ($newsletters as $item) {
            $newsletterArray = $this->getNewsletter($item['id']);
            if (empty($newsletterArray)) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:'.$item['id'], '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
                continue;
            }
            $params    = array(
                'newsletter_id' => $newsletterArray['id'],
                'subscriber_id' => $subscriberId,
            );
            $oldReport = $this->modx->getObject('vnewsReports', $params);
            if (!empty($oldReport)) {
                if ($reQueueExisting) {
                    $oldReport->set('status', 'queue');
                    $oldReport->set('status_logged_on', time());
                    $oldReport->save();
                }

                continue;
            }

            $params    = array_merge($params, array(
                'status'           => 'queue',
                'status_logged_on' => time(),
            ));
            $newReport = $this->modx->newObject('vnewsReports');
            $newReport->fromArray($params);
            $newReport->save();
        }
        return true;
    }

    /**
     * Remove this user from newletters' queues in a specified cateogry
     * @param   int     $subscriberId   subscriber's ID
     * @param   int     $catId          category ID
     * @return  boolean
     */
    public function removeSubscriberCategoryQueues($subscriberId, $catId)
    {
        $c      = $this->modx->newQuery('vnewsReports');
        $c->leftJoin('vnewsNewsletters', 'Newsletter', 'Newsletter.id = vnewsReports.newsletter_id');
        $c->leftJoin('vnewsNewslettersHasCategories', 'NewslettersHasCategories', 'NewslettersHasCategories.newsletter_id = Newsletter.id');
        $c->leftJoin('vnewsCategories', 'Category', 'Category.id = NewslettersHasCategories.category_id');
        $c->where(array(
            'vnewsReports.subscriber_id' => $subscriberId,
            'vnewsReports.status'        => 'queue',
            'Category.id'                => $catId,
        ));
        $queues = $this->modx->getCollection('vnewsReports', $c);
        if ($queues) {
            foreach ($queues as $queue) {
                $queue->remove();
            }
        }

        return true;
    }

    /**
     * Remove this user from all newletters' queues
     * @param   int     $subscriberId   subscriber's ID
     * @return  boolean
     */
    public function removeSubscriberQueues($subscriberId)
    {
        $c      = $this->modx->newQuery('vnewsReports');
        $c->where(array(
            'subscriber_id' => $subscriberId,
            'status'        => 'queue'
        ));
        $queues = $this->modx->getCollection('vnewsReports', $c);
        if ($queues) {
            foreach ($queues as $queue) {
                $queue->remove();
            }
        }
        // why the hell is this not working???
        // return $this->modx->removeCollection('vnewsReports', $c);

        return true;
    }

    /**
     * Remove all queues of this newsletter and it's descendants
     * @param   int     $newsletterId       newsletter's ID
     * @param   boolean $includeChildren    including all descendants? (default: false)
     * @param   boolean $includeSent        including all sent queues? (default: false)
     * @return  boolean
     */
    public function removeNewsletterQueues($newsletterId, $includeChildren = false, $includeSent = false)
    {
        $ids = array($newsletterId);
        if ($includeChildren) {
            $children = $this->modx->getCollection('vnewsNewsletters', array(
                'parent_id' => $newsletterId
            ));
            if ($children) {
                foreach ($children as $child) {
                    $ids[] = $child->get('id');
                }
            }
        }

        $c = $this->modx->newQuery('vnewsReports');
        $c->where(array(
            'newsletter_id:IN' => $ids
        ));
        if (!$includeSent) {
            $c->where(array(
                'status:=' => 'queue'
            ));
        }
        return $this->modx->removeCollection('vnewsReports', $c);
    }

    /**
     * Set all queues of the subscribers' reports
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @return  array   report's array
     */
    public function setQueues($todayOnly = false)
    {
        $c = $this->modx->newQuery('vnewsNewsletters');
        $c->where(array(
            'is_active' => 1,
        ));
        if ($todayOnly) {
            $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
            $c->where(array(
                'scheduled_for' => $today,
            ));
        }
        $newsletters = $this->modx->getCollection('vnewsNewsletters', $c);
        $time        = time();

        $outputReports = array();
        foreach ($newsletters as $newsletter) {
            $isRecurring = $newsletter->get('is_recurring');
            if ($isRecurring) {
                $newsletterArray = $this->getNewsletter($newsletter->get('id'));
                if (empty($newsletterArray)) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $this->getError(), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(false);
                    continue;
                }
            } else {
                $newsletterArray = $newsletter->toArray();
            }
            $subscribers = $this->modx->getObject('vnewsNewsletters', $newsletterArray['id'])->getSubscribers();
            if (empty($subscribers)) {
                continue;
            }
            foreach ($subscribers as $subscriber) {
                $report = $this->modx->getObject('vnewsReports', array(
                    'subscriber_id' => $subscriber['id'],
                    'newsletter_id' => $newsletterArray['id'],
                ));
                if ($report) {
                    continue;
                }

                $report = $this->modx->newObject('vnewsReports');
                $params = array(
                    'subscriber_id'    => $subscriber['id'],
                    'newsletter_id'    => $newsletterArray['id'],
                    'status'           => 'queue',
                    'status_logged_on' => $time,
                );
                $report->fromArray($params);
                if ($report->save() === false) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save report! '.print_r($params, true), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(false);
                } else {
                    $outputReports[] = $report->toArray();
                }
            }
        }

        return $outputReports;
    }

    /**
     * @deprecated since version 1.6.0-beta2
     * @see vnewsNewsletters::getSubscribers()
     * Get all subscribers of a newsletter
     * @param   int     $newsId newsletter's ID
     * @return  mixed   false|subscribers array
     */
    public function newsletterHasSubscribers($newsId)
    {
        $subscribersArray = array();

        $c = $this->modx->newQuery('vnewsSubscribers');
        $c->leftJoin('vnewsSubscribersHasCategories', 'SubscribersHasCategories', 'SubscribersHasCategories.subscriber_id = vnewsSubscribers.id');
        $c->leftJoin('vnewsCategories', 'Categories', 'Categories.id = SubscribersHasCategories.category_id');
        $c->leftJoin('vnewsNewslettersHasCategories', 'NewslettersHasCategories', 'NewslettersHasCategories.category_id = Categories.id');
        $c->leftJoin('vnewsNewsletters', 'Newsletters', 'Newsletters.id = NewslettersHasCategories.newsletter_id');
        $c->where(array(
            'vnewsSubscribers.is_active' => 1,
            'Newsletters.id'             => $newsId
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
     * Get queues
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @param   int     $limit      override limit of System Settings
     * @return  array   queues collection in array
     */
    public function getQueues($todayOnly = false, $limit = 0)
    {
        $c    = $this->modx->newQuery('vnewsReports');
        $c->leftJoin('vnewsNewsletters', 'Newsletters', 'Newsletters.id = vnewsReports.newsletter_id');
        $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsReports.subscriber_id');
        $c->select(array(
            'vnewsReports.*',
            'Subscribers.email_provider',
            'Newsletters.subject',
            'Newsletters.created_on',
            'Newsletters.scheduled_for',
            'Newsletters.is_recurring',
        ));
        $c->where(array(
            'vnewsReports.status:='   => 'queue',
            'Newsletters.is_active:=' => 1,
            'Newsletters.is_paused:=' => 0,
        ));
        $time = time();
//        $todayOnly = true;
        if ($todayOnly) {
            $today    = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
            $tomorrow = mktime(0, 0, 0, date('n'), date('j') + 1, date('Y'));
            $c->where(array(
                'Newsletters.scheduled_for:>' => $today,
                'Newsletters.scheduled_for:<' => $tomorrow,
            ));
        } else {
            $c->where(array(
                'Newsletters.scheduled_for:<' => $time + 1,
            ));
        }
        $c->where(array(
            'Newsletters.stopped_at:='    => 0,
            'OR:Newsletters.stopped_at:>' => $time,
        ));
        $limit = !empty($limit) ? $limit : intval($this->modx->getOption('virtunewsletter.email_limit'));
        if (!empty($limit)) {
            $c->limit($limit);
        }
        $c->sortby('Newsletters.id', 'asc');
        $c->sortby('vnewsReports.id', 'asc');
        $queues = $this->modx->getCollection('vnewsReports', $c);

        return $queues;
    }

    /**
     * Get multithreaded queues
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @param   int     $limit      override limit of System Settings
     * @return  array   queues collection in array
     */
    public function getMultiThreadedQueues($todayOnly = false, $limit = 0)
    {
        $c    = $this->modx->newQuery('vnewsNewsletters');
        $c->leftJoin('vnewsReports', 'Reports', 'Reports.newsletter_id = vnewsNewsletters.id');
        $c->select(array(
            'vnewsNewsletters.id',
            'count_queue' => "(SELECT COUNT(*) FROM {$this->modx->getTableName('vnewsReports')} WHERE newsletter_id = vnewsNewsletters.id)"
        ));
        $c->where(array(
            'vnewsNewsletters.is_active:=' => 1,
            'vnewsNewsletters.is_paused:=' => 0,
        ));
        $c->having("count_queue > 0");
        $time = time();
//        $todayOnly = true;
        if ($todayOnly) {
            $today    = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
            $tomorrow = mktime(0, 0, 0, date('n'), date('j') + 1, date('Y'));
            $c->where(array(
                'vnewsNewsletters.scheduled_for:>' => $today,
                'vnewsNewsletters.scheduled_for:<' => $tomorrow,
            ));
        } else {
            $c->where(array(
                'vnewsNewsletters.scheduled_for:<' => $time + 1,
            ));
        }
        $c->where(array(
            'vnewsNewsletters.stopped_at:='    => 0,
            'OR:vnewsNewsletters.stopped_at:>' => $time,
        ));
        $c->sortby('vnewsNewsletters.id', 'asc');
        $c->groupby('vnewsNewsletters.id');

        $newsletters = $this->modx->getCollection('vnewsNewsletters', $c);
        if (!$newsletters) {
            return;
        }
        $queues = array();
        foreach ($newsletters as $newsletter) {
            unset($c);
            $c     = $this->modx->newQuery('vnewsReports');
            $c->leftJoin('vnewsNewsletters', 'Newsletters', 'Newsletters.id = vnewsReports.newsletter_id');
            $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsReports.subscriber_id');
            $c->select(array(
                'vnewsReports.*',
                'Subscribers.email_provider',
                'Newsletters.subject',
                'Newsletters.created_on',
                'Newsletters.scheduled_for',
                'Newsletters.is_recurring',
            ));
            $c->where(array(
                'vnewsReports.status' => 'queue',
                'Newsletters.id'      => $newsletter->get('id'),
            ));
            $limit = !empty($limit) ? $limit : intval($this->modx->getOption('virtunewsletter.email_limit'));
            if (!empty($limit)) {
                $c->limit($limit);
            }
            $c->sortby('Newsletters.id', 'asc');
            $c->sortby('vnewsReports.id', 'asc');
            $nQueues = $this->modx->getCollection('vnewsReports', $c);
            $queues  = array_merge($queues, $nQueues);
        }

        return $queues;
    }

    /**
     * Switch queues among custom providers
     *
     * @param array     $queues getCollection's array
     * @param string    $emailProvider
     * @return boolean
     */
    public function sendQueuesToProvider($queues, $emailProvider = '')
    {
        if (empty($queues)) {
            return false;
        }
        if (empty($emailProvider)) {
            $emailProvider = $this->modx->getOption('virtunewsletter.email_provider');
        }
        $emailProvider = trim($emailProvider);

        $diffProviderQueues = array();
        if (!empty($emailProvider)) {
            $queuesArray   = array();
            $newsletterIds = array();
            foreach ($queues as $queue) {
                $queueArray = $queue->toArray('', false, true, true);
                if ($queueArray['status'] !== 'queue') {
                    continue;
                }
                $queueArray['email_provider'] = trim($queueArray['email_provider']);
                if ($queueArray['email_provider'] !== $emailProvider) {
                    if (!isset($diffProviderQueues[$queueArray['email_provider']]) || !is_array($diffProviderQueues[$queueArray['email_provider']])) {
                        $diffProviderQueues[$queueArray['email_provider']] = array();
                    }
                    $diffProviderQueues[$queueArray['email_provider']][] = $queue;
                    continue;
                }
                $queuesArray[]                               = $queueArray;
                $newsletterIds[$queueArray['newsletter_id']] = $queueArray['newsletter_id'];
            }
            foreach ($newsletterIds as $newsId) {
                $newsletterArray = $this->getNewsletter($newsId);
                $output          = array(
                    'newsletter_id' => $newsId,
                    'subject'       => $newsletterArray['subject'],
                    'created_on'    => $newsletterArray['created_on'],
                    'scheduled_for' => $newsletterArray['scheduled_for'],
                    'message'       => '',
                    'recipients'    => array(),
                );
                $result          = $this->sendToEmailProvider($emailProvider, $newsId, $queuesArray);
                if (!$result) {
                    $output['message'] = $this->getError();
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send queues! '.print_r($queuesArray, true), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(false);
                } else {
                    $output['recipients'] = $this->getResponses();
                    foreach ($output['recipients'] as $item) {
                        if (isset($item['email']) && isset($item['status'])) {
                            $subscriber = $this->modx->getObject('vnewsSubscribers', array(
                                'email' => $item['email'],
                            ));
                            if (!$subscriber) {
                                continue;
                            }
                            $subscriberArray = $subscriber->toArray();
                            $c               = $this->modx->newQuery('vnewsReports');
                            $c->where(array(
                                'newsletter_id' => $newsId,
                                'subscriber_id' => $subscriberArray['id'],
                            ));
                            $itemQueue       = $this->modx->getObject('vnewsReports', $c);
                            if (!$itemQueue) {
                                $itemQueue = $this->modx->newObject('vnewsReports');
                                $itemQueue->set('newsletter_id', $newsId);
                                $itemQueue->set('subscriber_id', $subscriberArray['id']);
                            }
                            $itemQueue->set('status_logged_on', time());
                            $itemQueue->set('status', $item['status']);
                            if ($itemQueue->save() === false) {
                                $this->modx->setDebug();
                                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! '.print_r($item, true), '', __METHOD__, __FILE__, __LINE__);
                                $this->modx->setDebug(false);
                            }
                        }
                    }
                }
                $outputReports[] = $output;
            }
        } else {
            foreach ($queues as $queue) {
                $queueArray = $queue->toArray('', false, true, true);
                if ($queueArray['status'] !== 'queue') {
                    continue;
                }
                $queueArray['email_provider'] = trim($queueArray['email_provider']);
                if ($queueArray['email_provider'] !== $emailProvider) {
                    if (!isset($diffProviderQueues[$queueArray['email_provider']]) || !is_array($diffProviderQueues[$queueArray['email_provider']])) {
                        $diffProviderQueues[$queueArray['email_provider']] = array();
                    }
                    $diffProviderQueues[$queueArray['email_provider']][] = $queue;
                    continue;
                }
                $newsletterArray = $this->getNewsletter($queueArray['newsletter_id']);
                $output          = array(
                    'newsletter_id' => $queueArray['newsletter_id'],
                    'subject'       => $newsletterArray['subject'],
                    'created_on'    => $newsletterArray['created_on'],
                    'scheduled_for' => $newsletterArray['scheduled_for'],
                    'message'       => '',
                    'recipients'    => array(),
                );
                $sent            = $this->sendNewsletter($queueArray['newsletter_id'], $queueArray['subscriber_id']);
                if ($sent) {
                    $queue->set('status_logged_on', time());
                    $queue->set('status', 'sent');
                    if ($queue->save() === false) {
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! '.print_r($queueArray, true), '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(false);
                    } else {
                        $output['recipients'] = $queueArray;
                    }
                } else {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! '.print_r($queueArray, true), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(false);
                    $output['message'] = 'Failed to send a queue! '.print_r($queueArray, true);
                }
                $outputReports[] = $output;
            }
        }
        if (!empty($diffProviderQueues)) {
            foreach ($diffProviderQueues as $k => $v) {
                $output        = $this->sendQueuesToProvider($v, $k);
                $outputReports = array_merge($outputReports, $output);
            }
        }

        return $outputReports;
    }

    /**
     * Process queue
     * @param   boolean $todayOnly  strict to today's queue (default: false)
     * @param   int     $limit      override limit of System Settings
     * @return  array   reports' outputs in array
     */
    public function processQueue($todayOnly = false, $limit = 0)
    {
        $isMultiThreaded = $this->modx->getOption('virtunewsletter.send_multithreaded');
        if ($isMultiThreaded) {
            $queues = $this->getMultiThreadedQueues($todayOnly, $limit);
        } else {
            $queues = $this->getQueues($todayOnly, $limit);
        }
        $outputReports = array();
        if ($queues) {
            $outputReports = $this->sendQueuesToProvider($queues);
        }

        return $outputReports;
    }

    /**
     * Subscribe a user
     * @param   array   $fields required information (email) and additional one (name)
     * @return  boolean
     */
    public function subscribe($fields)
    {
        if (!isset($fields[$this->config['emailKey']]) || empty($fields[$this->config['emailKey']])) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_err_ns');
            $this->setError($msg);
            return false;
        }
        $email = trim($fields[$this->config['emailKey']]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_err_invalid_email');
            $this->setError($msg);
            return false;
        }
        $registeredSubscriber = $this->modx->getObject('vnewsSubscribers', array(
            'email' => $email
        ));
        if ($registeredSubscriber) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_exists', array(
                'email' => $email
            ));
            $this->setError($msg);
            return false;
//            $name = $registeredSubscriber->get('name');
//            if (isset($fields[$this->config['nameKey']])) {
//                $name = $fields[$this->config['nameKey']];
//            }
//            $registeredSubscriber->set('name', $name);
//            $registeredSubscriber->set('is_active', 1);
//            return true;
        }
        $newSubscriber = $this->modx->newObject('vnewsSubscribers');

        $c    = $this->modx->newQuery('vnewsUsers');
        $c->leftJoin('modUserProfile', 'Profile', 'Profile.internalKey = vnewsUsers.id');
        $c->where(array(
            'Profile.email' => $email
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
                $name     = !empty($fullname) ? $fullname : $user->get('username');
            }
            $userId = $user->get('id');
        }

        $params = array(
            'user_id'   => $userId,
            'email'     => $email,
            'name'      => $name,
            'is_active' => 0, // wait to confirm
            'hash'      => $this->setHash($email)
        );

        $newSubscriber->fromArray($params);
        if ($newSubscriber->save() === false) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save a new subscriber! '.print_r($params, true), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_err_save');
            $this->setError($msg);
            return false;
        }

        /**
         * If defined in the input field, set user to the categories
         */
        if (isset($fields[$this->config['categoryKey']]) && !empty($fields[$this->config['categoryKey']])) {
            if (is_array($fields[$this->config['categoryKey']])) {
                foreach ($fields[$this->config['categoryKey']] as $category) {
                    $this->addSubscriberToCategory($newSubscriber->getPrimaryKey(), $category);
                }
            } else {
                $this->addSubscriberToCategory($newSubscriber->getPrimaryKey(), $fields[$this->config['categoryKey']]);
            }
        }

        $msg = $this->modx->lexicon('virtunewsletter.subscriber_suc_save');
        $this->setOutput($msg);

        $phs          = $newSubscriber->toArray();
        $phs['subid'] = $phs['id']; // to avoid confusion on template
        $phs          = array_merge($phs, array('act' => 'subscribe'));
        $this->setPlaceholders($phs);

        return true;
    }

    /**
     * Add subscriber to category
     * @param   int     $subscriberId   Subscriber's ID
     * @param   string  $category       Category's Name or ID
     */
    public function addSubscriberToCategory($subscriberId, $category)
    {
        $subscriber = $this->modx->getObject('vnewsSubscribers', $subscriberId);
        if (!$subscriber) {
            return false;
        }
        $c = $this->modx->newQuery('vnewsCategories');
        if (is_numeric($category)) {
            $c->where(array(
                'id' => $category,
            ));
        } else {
            $c->where(array(
                'name' => $category
            ));
        }
        $categoryObj = $this->modx->getObject('vnewsCategories', $c);
        if (!$categoryObj) {
            return false;
        }

        $params                   = array(
            'subscriber_id' => $subscriber->getPrimaryKey(),
            'category_id'   => $categoryObj->get('id')
        );
        $c                        = $this->modx->newQuery('vnewsSubscribersHasCategories');
        $c->where($params);
        $subscribersHasCategories = $this->modx->newObject('vnewsSubscribersHasCategories', $c);
        if ($subscribersHasCategories) {
            return true;
        }

        $subscribersHasCategories = $this->modx->newObject('vnewsSubscribersHasCategories');
        $subscribersHasCategories->fromArray($params);
        $addMany                  = array($subscribersHasCategories);
        $subscriber->addMany($addMany);
        if ($subscriber->save() === false) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to add subscriber to category! '.print_r($params, true), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        return true;
    }

    /**
     * Remove subscriber from category
     * @param   int     $subscriberId   Subscriber's ID
     * @param   string  $category       Category's Name or ID
     */
    public function removeSubscriberFromCategory($subscriberId, $category)
    {
        $c = $this->modx->newQuery('vnewsCategories');
        if (is_numeric($category)) {
            $c->where(array(
                'id' => $category,
            ));
        } else {
            $c->where(array(
                'name' => $category
            ));
        }
        $categoryObj = $this->modx->getObject('vnewsCategories', $c);
        if (!$categoryObj) {
            return false;
        }
        $subscribersHasCategories = $this->modx->getObject('vnewsSubscribersHasCategories', array(
            'subscriber_id' => $subscriberId,
            'category_id'   => $categoryObj->get('id')
        ));
        if (!$subscribersHasCategories) {
            return false;
        }
        return $subscribersHasCategories->remove();
    }

    /**
     * Get a subscriber
     * @param   array   $where  criteria in an array
     * @return  mixed   false|array of arguments
     */
    public function getSubscriber(array $where = array())
    {
        $c = $this->modx->newQuery('vnewsSubscribers');
        if (!empty($where)) {
            $c->where($where);
        }
        $subscriber = $this->modx->getObject('vnewsSubscribers', $c);
        if (!$subscriber) {
            $emailDebug = $this->modx->getOption('virtunewsletter.email_debug');
            if ($emailDebug) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unabled to get subscriber with criteria: '.print_r($where, true), '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
            }
            return false;
        }
        $subscriberArray          = $subscriber->toArray();
        $subscriberArray['subid'] = $subscriberArray['id'];
        return $subscriberArray;
    }

    /**
     * Get all subsribers
     * @param   array   $where  criteria in an array
     * @return  mixed   false|array of arguments
     */
    public function getAllSubscribers(array $where = array())
    {
        $c = $this->modx->newQuery('vnewsSubscribers');
        if (!empty($where)) {
            $c->where($where);
        }
        $subscribers = $this->modx->getCollection('vnewsSubscribers', $c);
        if (!$subscribers) {
            return false;
        }
        $allSubscribersArray = array();
        foreach ($subscribers as $subscriber) {
            $subscriberArray          = $subscriber->toArray();
            $subscriberArray['subid'] = $subscriberArray['id'];
            $allSubscribersArray[]    = $subscriberArray;
        }
        return $allSubscribersArray;
    }

    /**
     * Confirmation action
     * @param   array   $arguments  required arguments
     * @return  boolean
     */
    public function confirmAction($arguments)
    {
        $subscriber = $this->modx->getObject('vnewsSubscribers', array(
            'id'   => $arguments['id'],
            'hash' => $arguments['hash'],
        ));
        if (!$subscriber) {
            $msg = $this->modx->lexicon('virtunewsletter.subscriber_err_ne');
            $this->setError($msg);
            return false;
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
            case 'unsubscribing':
                $msg = $this->modx->lexicon('virtunewsletter.subscriber_unsubscribing');
                $this->setOutput($msg);
                break;
            case 'unsubscribe':
                if (isset($arguments['categories']) && !empty($arguments['categories'])) {
                    $categories = array_map('trim', @explode(',', $arguments['categories']));
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $c      = $this->modx->newQuery('vnewsSubscribersHasCategories');
                            $c->where(array(
                                'subscriber_id' => $subscriber->get('id'),
                                'category_id'   => $category,
                            ));
                            $subCat = $this->modx->getObject('vnewsSubscribersHasCategories', $c);
                            if ($subCat) {
                                $subCat->set('unsubscribed_on', time());
                                $subCat->save();
                                $this->removeSubscriberCategoryQueues($subscriber->get('id'), $category);
                            }
                        }
                    }
                } else {
                    $subscriber->set('is_active', 0);
                    $subscriber->save();
                    $this->removeSubscriberQueues($subscriber->get('id'));
                }
                $msg = $this->modx->lexicon('virtunewsletter.subscriber_suc_deactivated');
                $this->setOutput($msg);
                break;
            default:
                break;
        }

        return true;
    }

    /**
     * Hash for email
     * @param   string  $email  email address
     * @return  string  hashed string
     */
    public function setHash($email)
    {
        return str_rot13(base64_encode(hash('sha512', time().$email)));
    }

    /**
     * Apply CSS rules:
     * @param   string  $html   HTML content
     *
     * @return  string  parsed HTML content
     * @author  Josh Gulledge <jgulledge19@hotmail.com>
     */
    public function inlineCss($html)
    {
        if (empty($html)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'empty $html to create inline CSS', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        require_once MODX_CORE_PATH.'components/virtunewsletter/vendors/CssToInlineStyles/src/Specificity.php';
        require_once MODX_CORE_PATH.'components/virtunewsletter/vendors/CssToInlineStyles/src/Exception.php';
        require_once MODX_CORE_PATH.'components/virtunewsletter/vendors/CssToInlineStyles/src/CssToInlineStyles.php';

        // embedded CSS:
        preg_match_all('|<style(.*)>(.*)</style>|isU', $html, $css);
        $css_rules = '';
        if (!empty($css[2])) {
            foreach ($css[2] as $cssblock) {
                $css_rules .= $cssblock;
            }
        }

        $cssToInlineStyles = new TijsVerkoyen\CSSToInlineStyles\CSSToInlineStyles($html, $css_rules);
        $cssToInlineStyles->setEncoding($this->modx->getOption('mail_charset', null, 'UTF-8'));
        // the processed HTML
        $html              = $cssToInlineStyles->convert();

        return $html;
    }

    /**
     * Send newsletter emails
     * @param   int $newsId         Newsletter's ID
     * @param   int $subscriberId   Subscriber's ID
     * @return  boolean
     */
    public function sendNewsletter($newsId, $subscriberId)
    {
        $newsletterArray = $this->getNewsletter($newsId);
        if (empty($newsletterArray)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:'.$newsId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        if (empty($newsletterArray['content'])) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get content of the newsletter:'.print_r($newsletterArray, 1), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }

        $subscriber = $this->modx->getObject('vnewsSubscribers', $subscriberId);
        if (!$subscriber) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get subscriber w/ id:'.$subscriberId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        $subscriberArray = $subscriber->toArray();
        // vnewsNewslettersHasCategories
        $c               = $this->modx->newQuery('vnewsNewslettersHasCategories');
        $c->where(array(
            'newsletter_id' => $newsId,
        ));
        $newsCats        = $this->modx->getCollection('vnewsNewslettersHasCategories', $c);
        $categories      = array();
        if ($newsCats) {
            foreach ($newsCats as $newsCat) {
                $categories[] = $newsCat->get('category_id');
            }
        }
        $confirmLinkArgs   = $this->getSubscriber(array('email' => $subscriberArray['email']));
        $confirmLinkArgs   = array_merge($confirmLinkArgs, array('act' => 'unsubscribe'));
        $phs               = array_merge($subscriberArray, $confirmLinkArgs, array(
            // to avoid confusion on template
            'id'         => $newsId,
            'newsid'     => $newsId,
            'subid'      => $subscriberArray['id'],
            'categories' => @implode(',', $categories),
        ));
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $this->setPlaceholders($phs, $systemEmailPrefix);
        $content           = $this->processEmailMessage($newsId);
        return $this->sendMail($newsletterArray['subject'], $content, $subscriberArray['email']);
    }

    /**
     * Process content for email body
     * @param   int $newsId News' ID
     * @return  boolean
     * @todo    need to parse System Setting's and Context Setting's tags,
     */
    public function processEmailMessage($newsId)
    {
        $newsletterArray = $this->getNewsletter($newsId);
        if (empty($newsletterArray)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:'.$newsId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        if (empty($newsletterArray['content'])) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get content of the newsletter:'.print_r($subscriberArray, 1), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        $message = $newsletterArray['content'];
        $message = str_replace('%5B%5B%2B', '[[+', $message);
        $message = str_replace('%5D%5D', ']]', $message);
        $phs     = $this->getPlaceholders();
        $message = $this->parseTplCode($message, $phs);
        $message = $this->processElementTags($message);
        // remove tags: http://www.php.net/manual/en/domdocument.savehtml.php#85165
        $message = preg_replace('/^<!DOCTYPE.+? >/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $message));

        if ($this->modx->getOption('virtunewsletter.use_csstoinlinestyles') === 1) {
            $message = $this->inlineCss($message);
        }

        return $message;
    }

    /**
     * Send email
     * @param   string  $subject        Email's subject
     * @param   string  $message        Email's body
     * @param   string  $emailTo        Email address of the receiver
     * @param   string  $emailFrom      Email address of the sender
     * @param   string  $emailFromName  Name of the sender
     * @return boolean
     */
    public function sendMail($subject, $message, $emailTo, $emailFrom = '', $emailFromName = '')
    {
        if (!$this->modx->mail) {
            if (!$this->modx->getService('mail', 'mail.modPHPMailer')) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to load modPHPMailer class!', '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
                return false;
            }
        }
        if (empty($emailTo)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing email target!', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }

        if (empty($emailFrom)) {
            $emailFrom = $this->modx->getOption('virtunewsletter.email_sender');
            if (empty($emailFrom)) {
                $emailFrom = $this->modx->getOption('emailsender');
                if (empty($emailFrom)) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing email sender!', '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(false);
                    return false;
                }
            }
        }
        if (empty($emailFromName)) {
            $emailFromName = $this->modx->getOption('site_name');
        }

        if ($this->modx->getOption('virtunewsletter.email_debug')) {
            $this->modx->setDebug();
            $debugOutput = array(
                'subject'       => $subject,
                'message'       => $message,
                'emailTo'       => $emailTo,
                'emailFrom'     => $emailFrom,
                'emailFromName' => $emailFromName,
                'placeholders'  => $this->getPlaceholders(),
            );
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($debugOutput, true), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return true;
        }

        $this->modx->mail->set(modMail::MAIL_BODY, $message);
        $this->modx->mail->set(modMail::MAIL_FROM, $emailFrom);
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $emailFromName);
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);
        $this->modx->mail->address('to', $emailTo);
        $this->modx->mail->address('reply-to', $emailFrom);
        // https://support.google.com/mail/answer/180707?hl=en
        $x = explode('@', $emailFrom);
        $this->modx->mail->header('mailed-by:'.$x[1]);
        $this->modx->mail->header('signed-by:'.$x[1]);
        $this->modx->mail->setHTML(true);
        if (!$this->modx->mail->send()) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        $this->modx->mail->reset();

        return true;
    }

    /**
     * Send email through external email marketing provider
     * @param   string  $name   keyname of the provider, will be used as the controller's filename
     * @param   int     $newsId newsletter's ID
     * @param   array   $queuesArray    array of subscribers queue
     * @return  boolean
     */
    public function sendToEmailProvider($name, $newsId, $queuesArray)
    {
        $newsletterArray = $this->getNewsletter($newsId);
        if (empty($newsletterArray)) {
            $this->setError('Unable to get newsletter w/ id:'.$newsId);
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:'.$newsId, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }
        $subscribersArray = array();
        foreach ($queuesArray as $queue) {
            $subscriber = $this->modx->getObject('vnewsSubscribers', $queue['subscriber_id']);
            if ($subscriber) {
                $subscriberArray          = $subscriber->toArray();
                $subscriberArray['subid'] = $subscriberArray['id'];
                $subscribersArray[]       = $subscriberArray;
            } else if (isset($queue['email'])) {
                $subscribersArray[] = $queue;
            }
        }

        include_once $this->config['corePath'].'providers/emailprovider.class.php';
        $classFile = $this->config['corePath'].'providers/'.strtolower($name).'.class.php';
        if (!file_exists($classFile)) {
            $this->setError($classFile.' does not exist');
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, $classFile.' does not exist', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }

        $className = include_once $classFile;
        if (!class_exists($className)) {
            $this->setError('Unable to load :'.$name.' controller class');
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to load :'.$name.' controller class', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            return false;
        }

        $this->modx->virtunewsletter = $this;
        $object                      = new $className($this->modx);
        $object->setSender(array(
            'email_sender'      => $this->modx->getOption('virtunewsletter.email_sender'),
            'email_from_name'   => $this->modx->getOption('virtunewsletter.email_from_name'),
            'email_reply_to'    => $this->modx->getOption('virtunewsletter.email_reply_to'),
            'email_bcc_address' => $this->modx->getOption('virtunewsletter.email_bcc_address'),
        ));
        $object->setRecipients($subscribersArray);
        // to avoid confusion on template
        $newsletterArray['newsid']   = $newsletterArray['id'];
        $object->setNewsletter($newsletterArray);
        $object->setMessage(array(
            'subject' => $newsletterArray['subject'],
            'message' => $newsletterArray['content'],
        ));
        return $object->send();
    }

    /**
     * Get the raw content of a resource.
     * @see modRequest::getResource()
     * @see modResponse::outputContent()
     * @param   int     $resourceId ID of the resource
     * @param   array   $options    options
     * @return  string  content output
     */
    public function outputContent($resourceId, array $options = array())
    {
        $content = '';
        if ($this->modx->getRequest()) {
            $this->modx->resourceMethod     = "id";
            $this->modx->resourceIdentifier = $resourceId;
            $ctx                            = $this->modx->getObject('modResource', $resourceId)->get('context_key');
            $this->modx->context->set('key', $ctx);
            $this->modx->resource           = $this->modx->request->getResource($this->modx->resourceMethod, $this->modx->resourceIdentifier);
            if ($this->modx->resource && $this->modx->getResponse()) {
                if (!($this->modx->response->contentType = $this->modx->resource->getOne('ContentType'))) {
                    if ($this->modx->getDebug() === true) {
                        $this->modx->log(modX::LOG_LEVEL_DEBUG, "No valid content type for RESOURCE: ".print_r($this->modx->resource->toArray(), true));
                    }
                    $this->modx->log(modX::LOG_LEVEL_FATAL, "The requested resource has no valid content type specified.");
                }

                $this->modx->resource->_output         = $this->modx->resource->process();
                $this->modx->resource->_jscripts       = $this->modx->jscripts;
                $this->modx->resource->_sjscripts      = $this->modx->sjscripts;
                $this->modx->resource->_loadedjscripts = $this->modx->loadedjscripts;

                /* FIXME: only do this for HTML content ? */
                if (strpos($this->modx->response->contentType->get('mime_type'), 'text/html') !== false) {
                    /* Insert Startup jscripts & CSS scripts into template - template must have a </head> tag */
                    if (($js = $this->modx->getRegisteredClientStartupScripts()) && (strpos($this->modx->resource->_output, '</head>') !== false)) {
                        /* change to just before closing </head> */
                        $this->modx->resource->_output = preg_replace("/(<\/head>)/i", $js."\n\\1", $this->modx->resource->_output, 1);
                    }

                    /* Insert jscripts & html block into template - template must have a </body> tag */
                    if ((strpos($this->modx->resource->_output, '</body>') !== false) && ($js = $this->modx->getRegisteredClientScripts())) {
                        $this->modx->resource->_output = preg_replace("/(<\/body>)/i", $js."\n\\1", $this->modx->resource->_output, 1);
                    }
                }

                $this->modx->beforeRender();

                /* invoke OnWebPagePrerender event */
                if (!isset($options['noEvent']) || empty($options['noEvent'])) {
                    $this->modx->invokeEvent('OnWebPagePrerender');
                }

                $content = $this->modx->resource->_output;
            }
        }

        return $content;
    }

    /**
     * Prepare email content by parsing MODX's tags but leave the email's placeholders stay intact
     * @param   string  $content    raw content
     * @return  string  parsed content
     */
    public function prepareEmailContent($content)
    {
        // keeping the email's placeholders' tags
        $columns           = $this->modx->getSelectColumns('vnewsSubscribers');
        $columns           = str_replace('`', '', $columns);
        $phsArray          = array_map('trim', @explode(',', $columns));
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $phsArray          = array_merge($phsArray, array('newsid', 'subid', 'act'));
        $search            = array();
        $replace           = array();
        foreach ($phsArray as $phs) {
            $search[]  = '[[+'.$systemEmailPrefix.$phs;
            $replace[] = '&#91;&#91;+'.$systemEmailPrefix.$phs;
        }
        $content = str_replace($search, $replace, $content);
        // parsing what left
        $content = $this->processElementTags($content);
        // revert back the tags
        $search  = array();
        $replace = array();
        foreach ($phsArray as $phs) {
            $search[]  = '&#91;&#91;+'.$systemEmailPrefix.$phs;
            $replace[] = '[[+'.$systemEmailPrefix.$phs;
        }
        $content = str_replace($search, $replace, $content);

        return $content;
    }

    /**
     * Create next occurence of recurring newsletter
     * @param   int     $parentId   Newsletter's ID of the recurring parent
     * @return  mixed   false | array
     */
    public function createNextRecurrence($parentId)
    {
        $parentNewsletter = $this->modx->getObject('vnewsNewsletters', $parentId);
        if (!$parentNewsletter) {
            return false;
        }
        $parentNewsletterArray = $parentNewsletter->toArray();
        if (empty($parentNewsletterArray['is_recurring']) || empty($parentNewsletterArray['is_active'])) {
            return false;
        }
        // remove all queue for self
        $this->modx->removeCollection('vnewsReports', array(
            'newsletter_id' => $parentNewsletterArray['id'],
        ));
        $c                   = $this->modx->newQuery('vnewsNewsletters');
        $nextRecurrenceTime  = $this->nextRecurrenceTime($parentNewsletterArray['id']);
        $time                = time();
        $c->where(array(
            'parent_id'        => $parentNewsletterArray['id'],
            'scheduled_for:>=' => $time,
            'scheduled_for:<=' => $nextRecurrenceTime
        ));
        $c->limit(1);
        $c->sortby('scheduled_for', 'desc');
        $recurringNewsletter = $this->modx->getObject('vnewsNewsletters', $c);
        if (!$recurringNewsletter) {
            // checking the content of the latest recurrence.
            // if its content is as same as the current one, skip this
            $currentContent = $this->prepareEmailContent($parentNewsletterArray['content']);

            $c                = $this->modx->newQuery('vnewsNewsletters');
            $c->where(array(
                'parent_id' => $parentNewsletterArray['id'],
            ));
            $c->limit(1);
            $c->sortby('scheduled_for', 'desc');
            $latestNewsletter = $this->modx->getObject('vnewsNewsletters', $c);
            if ($latestNewsletter) {
                $latestContent = $this->prepareEmailContent($latestNewsletter->get('content'));
                if ($latestContent === $currentContent) {
                    $this->setError('$latestContent === $currentContent');
                    return false;
                }
            }

            $recurringNewsletter = $this->modx->newObject('vnewsNewsletters');
            $params              = array(
                'parent_id'     => $parentNewsletterArray['id'],
                'resource_id'   => $parentNewsletterArray['resource_id'],
                'subject'       => $this->processElementTags($parentNewsletterArray['subject']),
                'content'       => $currentContent,
                'created_by'    => $parentNewsletterArray['created_by'],
                'created_on'    => $time,
                'scheduled_for' => $nextRecurrenceTime,
                'is_recurring'  => 0,
                'is_active'     => $parentNewsletterArray['is_active'],
            );
            $recurringNewsletter->fromArray($params);
            if ($recurringNewsletter->save() === false) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save a new recurring newsletter! '.print_r($params, true), '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
                return false;
            }
            $categories                    = $parentNewsletter->getMany('NewslettersHasCategories');
            $recurringNewsletterCategories = array();
            foreach ($categories as $category) {
                $addCategory                     = $this->modx->newObject('vnewsNewslettersHasCategories');
                $catParams                       = array(
                    'newsletter_id' => $recurringNewsletter->getPrimaryKey(),
                    'category_id'   => $category->get('category_id')
                );
                $addCategory->fromArray($catParams);
                $recurringNewsletterCategories[] = $addCategory;
            }
            $recurringNewsletter->addMany($recurringNewsletterCategories);
            if ($recurringNewsletter->save() === false) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to add categories for the new recurring newsletter! ', '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
                return false;
            }
        }
        $newsletterArray = $recurringNewsletter->toArray();

        return $newsletterArray;
    }

    /**
     * Remove/delete all recurrences of ex-recurring newsletter
     * @param   int     $parentId   Newsletter's ID of the recurring parent
     * @return  mixed   false | array
     */
    public function removeAllRecurrences($parentId)
    {
        $collection = $this->modx->getCollection('vnewsNewsletters', array(
            'parent_id' => $parentId,
        ));
        if ($collection) {
            $ids = array();
            foreach ($collection as $item) {
                $ids[] = $item->get('id');
            }
            $this->modx->removeCollection('vnewsNewslettersHasCategories', array(
                'newsletter_id:IN' => $ids,
            ));
            $this->modx->removeCollection('vnewsReports', array(
                'newsletter_id:IN' => $ids,
            ));
        }
        return $this->modx->removeCollection('vnewsNewsletters', array(
                'parent_id' => $parentId,
        ));
    }
}
