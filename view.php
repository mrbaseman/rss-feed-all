<?php
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

/* example how to use: */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

require_once(WB_PATH .'/modules/rss-feed-all/include.php');

RssFeedAll_Render();
