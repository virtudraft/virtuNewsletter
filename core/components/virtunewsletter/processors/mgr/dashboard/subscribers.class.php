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
class VirtuNewsletterDashboardSubscribersProcessor extends modProcessor
{

    public function process()
    {
        $output = array(
            'subscribers_total'            => $this->modx->getCount('vnewsSubscribers'),
            'subscribers_active'           => $this->modx->getCount('vnewsSubscribers', array('is_active' => 1)),
            'subscribers_nonmember'        => $this->modx->getCount('vnewsSubscribers', array('user_id' => 0)),
            'subscribers_nonmember_active' => $this->modx->getCount('vnewsSubscribers', array('user_id' => 0, 'is_active' => 1)),
        );

        return $this->success('', $output);
    }
}

return "VirtuNewsletterDashboardSubscribersProcessor";
