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
class NewslettersCreateProcessor extends modObjectCreateProcessor {

    public $classKey = 'vnewsNewsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersCreate';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $subject = $this->getProperty('subject');
        if (empty($subject)) {
            $this->addFieldError('subject', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_subject'));
            return FALSE;
        }
        $resourceId = $this->getProperty('resource_id');
        if (empty($resourceId)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id'));
            return FALSE;
        }

        $categories = $this->getProperty('categories');
        $categories = @explode(',', $categories);
        if (empty($categories) || (isset($categories[0]) && empty($categories[0]))) {
            $this->addFieldError('categories', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_categories'));
            return FALSE;
        }

        $content = $this->modx->virtunewsletter->outputContent($resourceId);

        $isRecurring = $this->getProperty('is_recurring');
        if (!$isRecurring) {
            $content = $this->modx->virtunewsletter->prepareEmailContent($content);
        } else {
            $recurrenceNumber = $this->getProperty('recurrence_number');
            if (empty($recurrenceNumber)) {
                $this->addFieldError('recurrence_number', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_recurrence_number'));
                return FALSE;
            }
            $recurrenceRange = $this->getProperty('recurrence_range');
            if (empty($recurrenceRange)) {
                $this->addFieldError('recurrence_range', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_recurrence_range'));
                return FALSE;
            }
        }

        $content = str_replace(array('%5B%5B%2B', '%5D%5D'), array('[[+', ']]'), $content);
        $this->setProperty('content', $content);

        $this->setProperty('created_on', time());
        $userId = $this->modx->user->get('id');
        $this->setProperty('created_by', $userId);
        $schedule = $this->getProperty('scheduled_for');
        date_default_timezone_set('UTC');
        $schedule = strtotime($schedule);

        $this->setProperty('scheduled_for', $schedule);

        return parent::initialize();
    }

    /**
     * Override in your derivative class to do functionality before save() is run
     * @return boolean
     */
    public function afterSave() {
        $newsId = $this->object->getPrimaryKey();
        $categories = $this->getProperty('categories');
        $categories = @explode(',', $categories);
        if ($categories) {
            $addCats = array();
            foreach ($categories as $category) {
                $category = intval($category);
                $newsHasCat = $this->modx->newObject('vnewsNewslettersHasCategories');
                $newsHasCat->fromArray(array(
                    'newsletter_id' => $newsId,
                    'category_id' => $category,
                        ), NULL, TRUE, TRUE);
                $addCats[] = $newsHasCat;
            }
            $this->object->addMany($addCats);
            $this->object->save();
        }

        $isRecurring = $this->getProperty('is_recurring');
        if ($isRecurring) {
            $this->modx->virtunewsletter->createNextRecurrence($newsId);
        }
        $isActive = $this->getProperty('is_active');
        if ($isActive) {
            $this->modx->virtunewsletter->setNewsletterQueue($newsId);
        } else {
            $children = $this->object->getMany('Children');
            if ($children) {
                foreach ($children as $child) {
                    $child->set('is_active', 0);
                    $child->save();
                }
            }
        }

        return true;
    }

}

return 'NewslettersCreateProcessor';
