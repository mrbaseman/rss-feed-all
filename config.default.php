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


$RssFeedAll_limit = 15;  // Change this if you need more or less items returned

// Ban urls with unwanted words, Array of unwanted words in the url
// e.g. array("privat", "intranet", "intern")
$RssFeedAll_exclude = array();

// Include some page_id's explicitly,
// Array of page_ids of pages you want to have posted e.g. array( 3, 5, 7);
$RssFeedAll_include = array();

// for members of the admin group you might want to add a few more pages,
// only the difference is needed, e.g. you might want to add page_id = 11
// and all private pages: array( 11, "private");
$RssFeedAll_include_admin = array();

// also the exclude list will be merged, you can remove entries from the list
// by prepending a '-': array("-privat");
$RssFeedAll_exclude_admin = array();

// and the same for the superadmin account, e.g. here we remove the page_ids
// from above, and add all other "visibilities", "private" is already in the list
// array( -3, -5, -7, "hidden", "registered", "none");
$RssFeedAll_include_superadmin = array();

// and also the exclude list, remove the stuff from above:
// array("-intranet", "-intern");
$RssFeedAll_exclude_superadmin = array();

// we may restrict the output to urls that include specific words
// array("aktuell", "posts", "news");
$RssFeedAll_restrict = array();

// Topics Module
$topics_mod_name     = "topics";        // Name of the module

// OneForAll Module
$oneforall_mod_names    = "oneforall";  // Names of the oneforall modules. Seperated by a comma.

//$oneforall_mod_names  = "oneforall,projects,portfolio";       // Names of the oneforall modules.
                                                                // Seperated by a comma.
