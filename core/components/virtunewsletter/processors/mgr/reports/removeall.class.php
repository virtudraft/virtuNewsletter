<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2019 by goldsky <goldsky@virtudraft.com>
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
class RemoveAllReportsProcessor extends modProcessor {

    /** @var xPDOObject|modAccessibleObject $object The object being grabbed */
    public $object;

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.RemoveAllReportsProcessor';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsReports';

    /** @var string $primaryKeyField The primary key field to grab the object by */
    public $primaryKeyField = 'id';

    /** @var string $permission The Permission to use when checking against */
    public $permission = '';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');
    public $newsletter;

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $newsletterId = $this->getProperty('newsletter_id');
        $this->newsletter = $this->modx->getObject('vnewsNewsletters', $newsletterId);
        if (!$this->newsletter) {
            return 'Missing the newsletter';
        }

        return parent::initialize();
    }

    function process() {

        $ids = array($this->getProperty('newsletter_id'));
        $collection = $this->modx->getCollection('vnewsNewsletters', array(
            'parent_id' => $this->getProperty('newsletter_id'),
        ));
        if ($collection) {
            foreach ($collection as $item) {
                $ids[] = $item->get('id');
            }
        }
        $reports = $this->modx->getCollection('vnewsReports', array(
            'newsletter_id:IN' => $ids,
        ));
        if ($reports) {
            foreach ($reports as $report) {
                $report->remove();
            }
        }

        return $this->success();
    }

}

return 'RemoveAllReportsProcessor';
