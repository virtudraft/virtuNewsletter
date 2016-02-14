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
class SubscribersUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'vnewsSubscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersUpdate';

    /**
     * Override in your derivative class to do functionality after save() is run
     * @return boolean
     */
    public function afterSave() {
        $isActive = $this->getProperty('is_active');
        if ($isActive) {
            $this->modx->virtunewsletter->addSubscriberQueues($this->getProperty('id'));
        } else {
            $this->modx->virtunewsletter->removeSubscriberQueues($this->getProperty('id'));
        }
        return true;
    }

}

return 'SubscribersUpdateProcessor';