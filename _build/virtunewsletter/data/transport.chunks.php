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
 *
 * virtuNewsletter build script
 *
 * @package virtunewsletter
 * @subpackage build
 */
$chunks = array();

$chunks[0] = $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'cronreport.item',
    'description' => 'Item tpl for cron reports',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/cronreport.item.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'cronreport.wrapper',
    'description' => 'Wrapper tpl for cron reports',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/cronreport.wrapper.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

return $chunks;