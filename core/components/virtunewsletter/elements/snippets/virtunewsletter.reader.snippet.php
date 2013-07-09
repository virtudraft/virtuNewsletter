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
 *
 * @package virtunewsletter
 * @subpackage snippet
 */
$newsId = $modx->getOption('newsId', $scriptProperties, ($modx->getOption('newsid', $_GET)));
if (intval($newsId) < 1) {
    return;
}

$phsPrefix = $modx->getOption('phsPrefix', $scriptProperties, 'virtuNewsletter.reader.');
$itemTpl = $modx->getOption('itemTpl', $scriptProperties, '@CODE:[[+virtuNewsletter.reader.content]]');

$defaultVirtuNewsletterCorePath = $modx->getOption('core_path') . 'components/virtunewsletter/';
$virtuNewsletterCorePath = $modx->getOption('virtunewsletter.core_path', null, $defaultVirtuNewsletterCorePath);
$virtuNewsletter = $modx->getService('virtunewsletter', 'VirtuNewsletter', $virtuNewsletterCorePath . 'model/', $scriptProperties);

if (!($virtuNewsletter instanceof VirtuNewsletter))
    return '';

$virtuNewsletter->setConfigs($scriptProperties);

$newsletter = $virtuNewsletter->getNewsletter($newsId);
$phs = array();
foreach ($newsletter as $k => $v) {
    if ($v['is_recurring']) {
        $ctx = $this->modx->getObject('modResource', $v['resource_id'])->get('context_key');
        $url = $this->modx->makeUrl($v['resource_id'], $ctx, '', 'full');
        if (empty($url)) {
            return FALSE;
        }
        $v['content'] = file_get_contents($url);
    }

    $phs[$phsPrefix . $k] = $v;
}
$output = $virtuNewsletter->parseTpl($itemTpl, $phs);

return $output;