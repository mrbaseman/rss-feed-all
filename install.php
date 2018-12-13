<?php

/**
 *
 * @category        snippet
 * @package         rss feed all
 * @version         0.4.1
 * @authors         Martin Hecht (mrbaseman)
 * @copyright       (c) 2018, Martin Hecht (mrbaseman)
 * @link            https://github.com/WebsiteBaker-modules/rss-feed-all
 * @link            http://forum.wbce.org/viewtopic.php?id=655
 * @license         GNU General Public License
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.4 and higher
 *
 **/

    if (file_exists(WB_PATH."/modules/rss-feed-all/config.php"))
        rename(WB_PATH."/modules/rss-feed-all/config.php",
               WB_PATH."/modules/rss_feed_all/config.php");


    if (!file_exists(WB_PATH."/modules/rss_feed_all/config.php"))
        rename(WB_PATH."/modules/rss_feed_all/config.default.php",
               WB_PATH."/modules/rss_feed_all/config.php");

    if (file_exists(WB_PATH."/modules/rss-feed-all"))
        rm_full_dir(WB_PATH."/modules/rss-feed-all");

    $database->query("DELETE FROM ".TABLE_PREFIX."addons WHERE directory = 'rss-feed-all' AND type = 'module'");
