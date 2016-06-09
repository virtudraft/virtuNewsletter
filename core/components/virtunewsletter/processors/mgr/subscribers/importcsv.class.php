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
include_once MODX_CORE_PATH . 'model/modx/processors/browser/file/upload.class.php';

class vnewsSubscribersImportCsv extends modBrowserFileUploadProcessor {

    public function getLanguageTopics() {
        return array('file', 'virtunewsletter:cmp');
    }

    public function initialize() {
        $file = $this->getProperty('file');
        if (empty($file) || !isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return $this->modx->lexicon('virtunewsletter.import_err_file_empty');
        }
        $categories = $this->getProperty('categories');
        if (empty($categories)) {
            return $this->modx->lexicon('virtunewsletter.import_err_categories_empty');
        }

        $this->modx->setOption('upload_files', 'csv');
        $this->modx->setOption('base_path', $this->modx->virtunewsletter->config['corePath']);
        $path = 'imports/';
        $this->setProperty('path', $path);
        $this->setProperty('source', 0);
        return parent::initialize();
    }

    public function process() {
        if (!$this->getSource()) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        if (!$this->source->checkPolicy('create')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        $success = $this->source->uploadObjectsToContainer($this->getProperty('path'), $_FILES);

        if (empty($success)) {
            $msg = '';
            $errors = $this->source->getErrors();
            foreach ($errors as $k => $msg) {
                $this->modx->error->addField($k, $msg);
            }
            return $this->failure($msg);
        }

        ini_set("auto_detect_line_endings", 1);
        $filename = $_FILES['file']['name'];
        $props = $this->getProperties();
        $nameField = !empty($props['name']) ? trim($props['name']) : 'name';
        $emailField = !empty($props['email']) ? trim($props['email']) : 'email';
        $filepath = $this->modx->getOption('base_path') . $props['path'] . $filename;
        $row = 1;
        $time = time();
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, $props['delimiter'], $props['enclosure'], $props['escape'])) !== FALSE) {
                $num = count($data);
                if ($row === 1) {
                    for ($c = 0; $c < $num; $c++) {
                        if ($data[$c] === $nameField) {
                            $nameFieldIdx = $c;
                        } elseif ($data[$c] === $emailField) {
                            $emailFieldIdx = $c;
                        } else {
                            continue;
                        }
                    }
                } else {
                    if (!is_numeric($nameFieldIdx) && !is_numeric($emailFieldIdx)) {
                        return $this->failure($this->modx->lexicon('virtunewsletter.import_err_nf'));
                    }
                    if (empty($data[$emailFieldIdx]) || !filter_var($data[$emailFieldIdx], FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }
                    $subscriberEmail = strtolower($data[$emailFieldIdx]);
                    $subscriber = $this->modx->getObject('vnewsSubscribers', array(
                        'email:LIKE' => $subscriberEmail,
                    ));
                    if ($subscriber) {
                        continue;
                    }
                    $subscriber = $this->modx->newObject('vnewsSubscribers');
                    $userProfile = $this->modx->getObject('modUserProfile', array(
                        'email:LIKE' => $subscriberEmail,
                    ));
                    if ($userProfile) {
                        $userProfileArray = $userProfile->toArray();
                        $userId = $userProfileArray['internalKey'];
                        $name = !empty($userProfileArray['fullname']) ? $userProfileArray['fullname'] : $userProfile->getOne('User')->get('username');
                    } else {
                        $userId = 0;
                        $name = $data[$nameFieldIdx];
                    }
                    $params = array(
                        'user_id' => $userId,
                        'name' => $name,
                        'email' => $subscriberEmail,
                        'is_active' => $props['is_active'],
                        'hash' => $this->modx->virtunewsletter->setHash($subscriberEmail)
                    );
                    $subscriber->fromArray($params);
                    if ($subscriber->save() === false) {
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unabled to import subscriber: ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(FALSE);
                        continue;
                    }

                    $subscriberId = $subscriber->getPrimaryKey();
                    foreach ($props['categories'] as $catId) {
                        $subCat = $this->modx->newObject('vnewsSubscribersHasCategories');
                        $params = array(
                            'subscriber_id' => $subscriberId,
                            'category_id' => $catId,
                            'subscribed_on' => $time
                        );
                        $subCat->fromArray($params);
                        if ($subCat->save() === false) {
                            $this->modx->setDebug();
                            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unabled to save vnewsSubscribersHasCategories: ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
                            $this->modx->setDebug(FALSE);
                            continue;
                        }
                    }
                }
                $row++;
            }
            fclose($handle);
        } else {
            return $this->failure($this->modx->lexicon('virtunewsletter.read_file_err'));
        }

        return $this->success($this->modx->lexicon('virtunewsletter.subscribers_imported'));
    }

}

return 'vnewsSubscribersImportCsv';
