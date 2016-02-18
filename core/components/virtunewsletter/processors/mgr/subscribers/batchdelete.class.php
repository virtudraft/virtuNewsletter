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
class BatchRemoveSubscribersProcessor extends modProcessor {

    /** @var xPDOObject|modAccessibleObject $object The object being grabbed */
    public $object;

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.BatchRemoveSubscribersProcessor';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsSubscribers';

    /** @var string $primaryKeyField The primary key field to grab the object by */
    public $primaryKeyField = 'id';

    /** @var string $permission The Permission to use when checking against */
    public $permission = '';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $ids = $this->getProperty('ids');
        if (empty($ids)) {
            return $this->modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id');
        }

        return parent::initialize();
    }

    function process() {
        $ids = @explode(',', $this->getProperty('ids'));
        foreach ($ids as $id) {
            $subscriber = $this->modx->getObject('vnewsSubscribers', $id);
            if ($subscriber) {
                $subscriber->remove();
            }
        }
        return $this->success();
    }

}

return 'BatchRemoveSubscribersProcessor';
