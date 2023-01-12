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
class TemplateUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'vnewsTemplates';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.TemplateUpdate';

    public function initialize() {
        $c = $this->modx->newQuery($this->classKey);
        $name = $this->getProperty('name', false);
        if (!empty($name)) {
            $c->where(array(
                'name' => $name,
            ));
        }
        $cultureKey = $this->getProperty('culture_key', false);
        if (!empty($cultureKey)) {
            $c->where(array(
                'culture_key' => $cultureKey,
            ));
        }
        $this->object = $this->modx->getObject($this->classKey, $c);
        if (empty($this->object)) {
            $this->object = $this->modx->newObject($this->classKey);
        }

        return true;
    }

}

return 'TemplateUpdateProcessor';