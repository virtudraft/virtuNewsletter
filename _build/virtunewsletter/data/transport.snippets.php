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
 * @package virtunewsletter
 * @subpackage build
 */

/**
 * @param   string  $filename   filename
 * @return  string  file content
 */
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = str_replace('<?php', '', $o);
    $o = str_replace('?>', '', $o);
    $o = trim($o);
    return $o;
}

$snippets = array();

$snippets[0] = $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'property_preprocess' => 0,
    'name' => 'virtuNewsletter.subscribe',
    'description' => 'Subscription form processor',
    'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/virtunewsletter.subscribe.snippet.php'),
        ), '', true, true);
$properties = include $sources['properties'] . 'virtunewsletter.subscribe.snippet.properties.php';
$snippets[0]->setProperties($properties);
unset($properties);

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'property_preprocess' => 0,
    'name' => 'virtuNewsletter.reader',
    'description' => 'To read the newsletter on the web',
    'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/virtunewsletter.reader.snippet.php'),
        ), '', true, true);
$properties = include $sources['properties'] . 'virtunewsletter.reader.snippet.properties.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'property_preprocess' => 0,
    'name' => 'virtuNewsletter.confirm',
    'description' => 'To process confirmation action',
    'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/virtunewsletter.confirm.snippet.php'),
        ), '', true, true);
$properties = include $sources['properties'] . 'virtunewsletter.confirm.snippet.properties.php';
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'property_preprocess' => 0,
    'name' => 'virtuNewsletter.list',
    'description' => 'To list newsletters in front-end',
    'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/virtunewsletter.list.snippet.php'),
        ), '', true, true);
$properties = include $sources['properties'] . 'virtunewsletter.list.snippet.properties.php';
$snippets[3]->setProperties($properties);
unset($properties);

return $snippets;