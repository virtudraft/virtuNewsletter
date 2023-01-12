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
ignore_user_abort(1); // run script in background
set_time_limit(86400); // run script for 1 day

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

class VirtuNewsletterSetProcessor extends modProcessor
{

    public function initialize()
    {
        $siteId = $this->getProperty("site_id");
        if (!$siteId) {
            die('Missing authentification!');
        }
        if ($siteId !== $this->modx->site_id) {
            die('Wrong authentification!');
        }
        parent::initialize();
    }

    public function process()
    {
        if (ob_get_level() == 0) {
            ob_start();
        }
        $isTodayOnly       = $this->getProperty("today_only");
        $todayOnly         = !!$isTodayOnly ? true : false;
        $output            = $this->modx->virtunewsletter->setQueues($todayOnly);
        $cronReportEnabled = $this->modx->getOption('virtunewsletter.cronreport.enabled', null, 1);
        if ($cronReportEnabled) {
            return $this->success('', $output);
        }
        ob_end_flush();
        exit;
    }
}

return "VirtuNewsletterSetProcessor";
