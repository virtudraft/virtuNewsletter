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
class SubscribersExportCsvProcessor extends modProcessor {

    public function checkPermissions() {
        return true;
    }

    public function getLanguageTopics() {
        return array('virtunewsletter:cmp');
    }

    public function process() {
        $download = $this->getProperty('download');
        if (!empty($download)) {
            $o = $this->download($download);
        } else {
            $o = $this->export();
        }
        return $o;
    }

    /**
     * Download the file
     *
     * @param string $file
     * @return bool|string
     */
    public function download($file) {
        $fileName = $this->modx->virtunewsletter->config['corePath'] . 'export/' . $file;
        if (!is_file($fileName))
            return '';
        $output = file_get_contents($fileName);
        if (empty($output))
            return '';

        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="subscribers.csv"');
        return $output;
    }

    /**
     * Export the properties into a temporary export file
     *
     * @return mixed
     */
    public function export() {
        $collections = $this->modx->getCollection('vnewsSubscribers');
        if (!$collections) {
            return $this->failure('No subscribers is recorded');
        }
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="subscribers.csv"');
        $f = 'subscribers.csv';
        $out = fopen($this->modx->virtunewsletter->config['corePath'] . 'export/' . $f, 'w');
        $columns = $this->modx->getSelectColumns('vnewsSubscribers');
        $columns = str_replace('`', '', $columns);
        $columnsArray = array_map('trim', @explode(',', $columns));
        foreach ($columnsArray as $k => $v) {
            if ($v === 'hash') {
                unset($columnsArray[$k]);
            }
        }
        $columnsArray = array_merge($columnsArray, array('categories', 'usergroups'));
        $columnsArray = array_values($columnsArray);

        fputcsv($out, $columnsArray);
        foreach ($collections as $item) {
            $itemArray = $item->toArray();
            foreach ($itemArray as $k => $v) {
                if ($k === 'hash') {
                    unset($itemArray[$k]);
                }
            }
            $itemArray = array_merge($itemArray, array(
                @implode(', ', $item->getCategoryNames()),
                @implode(', ', $item->getUserGroupNames()),
            ));
            $itemArray = array_values($itemArray);
            fputcsv($out, $itemArray);
        }
        fclose($out);

        return $this->success($f);
    }

}

return 'SubscribersExportCsvProcessor';
