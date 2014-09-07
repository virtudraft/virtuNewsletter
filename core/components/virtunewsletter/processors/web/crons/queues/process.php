<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2014 by goldsky <goldsky@virtudraft.com>
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
if (!isset($_GET['site_id'])) {
    die('Missing authentification!');
}
if ($_GET['site_id'] !== $modx->site_id) {
    die('Wrong authentification!');
}

$todayOnly = isset($_GET['today_only']) && ($_GET['today_only'] == 1) ? true : false;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? intval($_GET['limit']) : 0;

$modx->virtunewsletter->setQueues($todayOnly);
$reports = $modx->virtunewsletter->processQueue($todayOnly, $limit);

$outputType = isset($_GET['output_type']) && $_GET['output_type'] === 'json' ? 'json' : 'html';
if ($outputType === 'json') {
    return $this->success('', $reports);
} else {
    $getItems = isset($_GET['get_items']) && $_GET['get_items'] === 1 ? 1 : 0;
    $output = '';
    if (!empty($reports)) {
        $phs = array(
            'newsletter_id' => $reports[0]['newsletter_id'],
            'subject' => $reports[0]['subject'],
            'created_on' => $reports[0]['created_on'],
            'scheduled_for' => $reports[0]['scheduled_for'],
            'count' => count($reports)
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
        $output = $modx->virtunewsletter->parseTpl('cronreport.wrapper', $phs);
        $output = $modx->virtunewsletter->processElementTags($output);
    }
    return $output;
}