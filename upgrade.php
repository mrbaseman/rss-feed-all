<?php

/*
__M4_FILE__
*/

/**
 *
 * @category        snippet
 * @package         rss feed all
 * @version         0.3.0
 * @authors         Martin Hecht (mrbaseman)
 * @copyright       (c) 2018, Martin Hecht (mrbaseman)
 * @link            https://github.com/WebsiteBaker-modules/rss-feed-all
 * @link            http://forum.wbce.org/viewtopic.php?id=655
 * @license         GNU General Public License
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.4 and higher
 *
 **/

    if (!file_exists(WB_PATH."/modules/rss-feed-all/config.php"))
        rename(WB_PATH."/modules/rss-feed-all/config.default.php",
               WB_PATH."/modules/rss-feed-all/config.php");

