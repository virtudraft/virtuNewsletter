<?php
/**
 * virtuNewsletter
 *
 * Copyright 2013-2023 by goldsky <goldsky@virtudraft.com>
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
 */
/**
 * @package virtunewsletter
 * @subpackage processor
 */
ignore_user_abort(1); // run script in background
set_time_limit(86400); // run script for 1 day

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

class VirtuNewsletterWebCronQueuesProcessProcessor extends modProcessor
{

    public function initialize()
    {
        $siteId = $this->getProperty("site_id");
        if (!$siteId) {
            die('Missing authentification!');
        }
        if ($siteId !== $this->modx->site_id) {
            die('Wrong authentification!');
        }
        return parent::initialize();
    }

    public function process()
    {
        $isTodayOnly = $this->getProperty("today_only");
        $todayOnly   = !!$isTodayOnly ? true : false;
        $limit       = intval($this->getProperty("limit", 0));

        ob_start();
        $reports = $this->modx->virtunewsletter->processQueue($todayOnly, $limit);

        $outputType        = $this->getProperty('output_type');
        $outputType        = $outputType === 'json' ? 'json' : 'html';
        $cronReportEnabled = $this->modx->getOption('virtunewsletter.cronreport.enabled', null, 1);
        if ($cronReportEnabled) {
            if ($outputType === 'json') {
                header('Content-type: application/json');
                return $this->success('', $reports);
            } else {
                header('Content-Type: text/html; charset=utf-8');
                $getItems = $this->getProperty('get_items');
                if (!$getItems) {
                    $getItems = $this->modx->getOption('virtunewsletter.cronreport.getItems', null, 0);
                }
                $output = '';
                if (!empty($reports)) {
                    $outputArray          = array();
                    $cronReportItemTpl    = $this->modx->getOption('virtunewsletter.cronreport.itemTpl', null, 'cronreport.item');
                    $cronReportWrapperTpl = $this->modx->getOption('virtunewsletter.cronreport.wrapperTpl', null, 'cronreport.wrapper');
                    foreach ($reports as $report) {
                        $phs = array(
                            'newsletter_id' => $report['newsletter_id'],
                            'subject'       => $report['subject'],
                            'created_on'    => $report['created_on'],
                            'scheduled_for' => $report['scheduled_for'],
                            'count'         => count($report['recipients'])
                        );
                        if ($getItems) {
                            $itemArray = array();
                            foreach ($reports as $report) {
                                $parseTpl    = $this->modx->virtunewsletter->parseTpl($cronReportItemTpl, $report);
                                $itemArray[] = $this->modx->virtunewsletter->processElementTags($parseTpl);
                            }
                            $phs['items'] = @implode('', $itemArray);
                        } else {
                            $phs['items'] = '';
                        }
                        $parsed        = $this->modx->virtunewsletter->parseTpl($cronReportWrapperTpl, $phs);
                        $outputArray[] = $this->modx->virtunewsletter->processElementTags($parsed);
                    }
                    $output = @implode("\n", $outputArray);
                } else {
                    $output = "No queue";
                }

                echo $output;
            }
        }

        ob_end_flush();
        exit;
    }
}

return "VirtuNewsletterWebCronQueuesProcessProcessor";