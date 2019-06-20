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
class ReportsRemoveProcessor extends modObjectRemoveProcessor {

    public $classKey = 'vnewsReports';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.ReportsRemove';

    public function initialize() {
        $newsletterId = $this->getProperty('newsletter_id');
        $subscriberId = $this->getProperty('subscriber_id');
        if (empty($newsletterId) || empty($subscriberId))
            return $this->modx->lexicon($this->objectType . '_err_ns');

        $this->object = $this->modx->getObject($this->classKey, array(
            'newsletter_id' => $newsletterId,
            'subscriber_id' => $subscriberId,
        ));
        if (empty($this->object)) {
            return $this->modx->lexicon($this->objectType . '_err_nfs', array(
                        'newsletter_id' => $newsletterId,
                        'subscriber_id' => $subscriberId,
            ));
        }

        if ($this->checkRemovePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('remove')) {
            return $this->modx->lexicon('access_denied');
        }
        return true;
    }

}

return 'ReportsRemoveProcessor';