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

$reports = $modx->virtunewsletter->processQueue();

$outputType = isset($_GET['outputtype']) && $_GET['outputtype'] === 'json' ? 'json' : 'html';
if ($outputType === 'json') {
    return $this->success('', $reports);
} else {
    $getItems = isset($_GET['getitems']) && $_GET['getitems'] === 1 ? 1 : 0;
    $output = '';
    if (!empty($reports)) {
        $phs = array(
            'newsletter_id' => $reports[0]['newsletter_id'],
            'subject' => $reports[0]['subject'],
            'current_occurrence_time' => $reports[0]['current_occurrence_time'],
            'next_occurrence_time' => $reports[0]['next_occurrence_time'],
            'count' => count($reports)
        );
        if ($getItems) {
            $itemArray = array();
            foreach ($reports as $report) {
                $itemArray[] = $modx->virtunewsletter->parseTpl('cronreport.item', $report);
            }
            $phs['items'] = @implode('', $itemArray);
        }
        $output = $modx->virtunewsletter->parseTpl('cronreport.wrapper', $phs);
    }
    return $output;
}