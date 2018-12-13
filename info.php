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

$module_directory   = 'rss_feed_all';
$module_name        = 'rss feed all';
$module_function    = 'snippet';
$module_version     = '0.4.1';
$module_platform    = 'WebsiteBaker 2.8.x';
$module_author      = 'Martin Hecht (mrbaseman)';
$module_license     = 'GNU General Public License';
$module_description = 'This module provides rss feed for all pages of a WB installation, including the contents (e.g. posts) in News, Bakery, Catalog, Portfolio, Topics, Showcase, OneForAll, Procalendar';

/* Usage:

just install the module and adjust the values in config.php to your needs
(see the comments in that file for explanations) the file is shipped as
config.default.php to avoid overwriting your adjustments during module upgrade.

Your rss feeds are available at the url WB_URL/modules/rss_feed_all/view.php

More advanced hint:
You can also call RssFeedAll_Render() for instance in a code2 section on a hidden
page with a blank template assigned. When using it this way, make sure that you
suppress the section anchor, e.g. using an output filter.

*/

/*
 *      CHANGELOG
 *
 *      0.4.1   2018-12-13      - correctly reflect the generator software
 *                              - fix url in how-to-use description in info.php
 *
 *      0.4.0   2018-12-13      - move module directory to rss_feed_all
 *                              - move changelog down
 *                              - add a description how to use in info.php
 *
 *      0.3.0   2018-11-16      - allow to restrict output to links containing specific words
 *                              - bugfix: parameter order for topics feed function
 *                              - bugfix: lastchange has to point to lastmod for some modules
 *                              - provide a pre-filled config.php file for site specific setup
 *
 *      0.2.1   2016-09-05      - improve documentation of config section
 *                              - allow a few strings in include list for convenience
 *
 *      0.2.0   2016-09-04      - correctly treat visibility of pages
 *                              - special settings for superadmin/admin group members
 *
 *      0.1.1   2016-08-26      - take care of $RssFeedAll_limit, introduce
 *                                $RssFeedAll_include, optimize function calls,
 *                                Bugfix for include of local config file
 *      0.1.0   2016-08-23      - finished the first release, supporting News, Bakery,
 *                                Catalog, Portfolio, Topics, Showcase, OneForAll,
 *                                Procalendar
 *
 *      0.0.1   2016-08-02      - first code import (parts re-used from google_sitemap)
 *
 */
