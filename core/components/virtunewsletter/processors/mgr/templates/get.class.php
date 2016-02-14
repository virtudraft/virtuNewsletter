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
class TemplateGetProcessor extends modObjectGetProcessor {

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.TemplateGet';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsTemplates';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $c = $this->modx->newQuery($this->classKey);
        $primaryKey = $this->getProperty($this->primaryKeyField, false);
        if (!empty($primaryKey)) {
            $c->where(array(
                $this->primaryKeyField => $primaryKey
            ));
        }
        $cultureKey = $this->getProperty('culture_key');
        if (!empty($cultureKey)) {
            $c->where(array(
                'culture_key' => $cultureKey
            ));
        }
        $name = $this->getProperty('name');
        if (!empty($name)) {
            $c->where(array(
                'name' => $name
            ));
        }
        $this->object = $this->modx->getObject($this->classKey, $c);
        if (empty($this->object)) {
            return $this->modx->lexicon($this->objectType . '_err_nfs', array($this->primaryKeyField => $primaryKey));
        }

        return true;
    }

}

return 'TemplateGetProcessor';
