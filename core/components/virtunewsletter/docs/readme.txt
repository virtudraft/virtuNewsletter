--------------------
Package: virtuNewsletter
--------------------
Author: goldsky <goldsky@virtudraft.com>
        http://twitter.com/_goldsky

This is a newsletter system for MODX Revolution.
To set the cron job, use the web accessible connector file
(assets/components/virtunewsletter/conn/web.php) with these arguments:
- action=web/crons/queues/process
- site_id=......[your installation's site ID].......

To get your site ID, check your core/config.inc.php file, and find out the
$site_id variable.

Official documentation is https://rtfm.modx.com/extras/revo/virtunewsletter

Resources:
* https://www.mail-tester.com
* http://www.openspf.org
* http://www.dkim.org
