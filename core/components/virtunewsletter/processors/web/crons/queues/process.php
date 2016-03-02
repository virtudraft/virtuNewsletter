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
 */
/**
 * @package virtunewsletter
 * @subpackage processor
 */
if (!isset($_REQUEST['site_id'])) {
    die('Missing authentification!');
}
if ($_REQUEST['site_id'] !== $modx->site_id) {
    die('Wrong authentification!');
}

$todayOnly = isset($_REQUEST['today_only']) && ($_REQUEST['today_only'] == 1) ? true : false;
$limit = isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 0;

$modx->virtunewsletter->setQueues($todayOnly);
$reports = $modx->virtunewsletter->processQueue($todayOnly, $limit);

$outputType = isset($_REQUEST['output_type']) && $_REQUEST['output_type'] === 'json' ? 'json' : 'html';
if ($outputType === 'json') {
    return $this->success('', $reports);
} else {
    $getItems = isset($_REQUEST['get_items']) && $_REQUEST['get_items'] === 1 ? 1 : 0;
    $output = '';
    if (!empty($reports)) {
        $outputArray = array();
        foreach ($reports as $report) {
            $phs = array(
                'newsletter_id' => $report['newsletter_id'],
                'subject' => $report['subject'],
                'created_on' => $report['created_on'],
                'scheduled_for' => $report['scheduled_for'],
                'count' => count($report['recipients'])
            );
            if ($getItems) {
                $itemArray = array();
                foreach ($reports as $report) {
                    $parseTpl = $modx->virtunewsletter->parseTpl('cronreport.item', $report);
                    $itemArray[] = $modx->virtunewsletter->processElementTags($parseTpl);
                }
                $phs['items'] = @implode('', $itemArray);
            } else {
                $phs['items'] = '';
            }
            $parsed = $modx->virtunewsletter->parseTpl('cronreport.wrapper', $phs);
            $outputArray[] = $modx->virtunewsletter->processElementTags($parsed);
        }
        $output = @implode("\n", $outputArray);
    }

    return $output;
}