<?php
/**
 *
 * @category        snippet
 * @package         rss feed all
 * @version         0.2.1
 * @authors         Martin Hecht (mrbaseman)
 * @copyright       (c) 2016, Martin Hecht (mrbaseman)
 * @link            http://forum.wbce.org/viewtopic.php?id=655
 * @license         GNU General Public License
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.4 and higher
 *
 **/



// Include WB files
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');


/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
        // Stop this file being access directly
        die('<head><title>Access denied</title></head><body><h2 style="color:red;margin:3em auto;text-align:center;">Cannot access this file directly</h2></body></html>');
}
/* -------------------------------------------------------- */



require_once(WB_PATH.'/framework/class.frontend.php');


if (!function_exists('RssFeedAll_Render')) {


// the "main" function of this snippet
// ***********************************


    function RssFeedAll_Render($debug=FALSE){


        // -------------------------------------------------------------------------
        // CONFIGURATION SECTION - copy this part into a local config.php 
        // -------------------------------------------------------------------------

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
        
        // Topics Module
        $topics_mod_name     = "topics";        // Name of the module
        
        // OneForAll Module
        $oneforall_mod_names    = "oneforall";  // Names of the oneforall modules. Seperated by a comma.

        //$oneforall_mod_names  = "oneforall,projects,portfolio";       // Names of the oneforall modules. 
                                                                        // Seperated by a comma.

        // -------------------------------------------------------------------------
        // END OF CONFIGURATION
        // -------------------------------------------------------------------------

        // include module specific config file if it exists
        if(file_exists(dirname(__FILE__).'/config.php')){
            include(dirname(__FILE__).'/config.php');
        }

        global $admin;

        // Vars
        $output_array = array();
        $debug_info   = array();
        $public       = array();
        $modules      = array();
        $counter      = 0;
        $charset      = '';
        $page_counter = 0;


        $wb = new frontend();
        $wb->get_page_details();
        $wb->get_website_settings();

        //checkout if a charset is defined otherwise use UTF-8
        if(defined('DEFAULT_CHARSET')) {
            $charset=DEFAULT_CHARSET;
        } else {
            $charset='utf-8';
        }


        // get authenticated user data
        if(isset($admin) AND $admin->is_authenticated() AND $admin->get_user_id() > 0) {
            // user is member of admin group:
            if(in_array(1, $admin->get_groups_id())){
                $RssFeedAll_include = RssFeedAll_MergeArrays( $RssFeedAll_include, $RssFeedAll_include_admin );
                $RssFeedAll_exclude = RssFeedAll_MergeArrays( $RssFeedAll_exclude, $RssFeedAll_exclude_admin );
            }
            // user is superademin
            if($admin->get_user_id() == 1) {
                $RssFeedAll_include = RssFeedAll_MergeArrays( $RssFeedAll_include, $RssFeedAll_include_superadmin);
                $RssFeedAll_exclude = RssFeedAll_MergeArrays( $RssFeedAll_exclude, $RssFeedAll_exclude_superadmin);
            }
        } 

        // call pages
        RssFeedAll_Pages($output_array, $debug_info, $public, $modules, $counter, $RssFeedAll_exclude, $RssFeedAll_include, $wb);

        // All by WB sections currently used modules
        $modules = array_unique($modules);

        // Count pages excluding module pages
        $page_counter = $counter;

        //  call modules
        if (in_array('news', $modules)) 
            RssFeedAll_News( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb ); 

        if (in_array('bakery', $modules))
            RssFeedAll_Bakery( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb ); 

        if (in_array('catalogs', $modules))
            RssFeedAll_Catalog( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb ); 

        if (in_array('portfolio', $modules))
            RssFeedAll_Portfolio( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb ); 

        if (in_array($topics_mod_name, $modules))
            RssFeedAll_Topics( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $topics_mod_name, $wb ); 

        if (in_array('showcase', $modules))
            RssFeedAll_Showcase( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb ); 

        $oneforall_mods = explode(',',$oneforall_mod_names);
        foreach ( $oneforall_mods as $oneforall_mod_name) {
            $oneforall_mod_name = trim($oneforall_mod_name);
            if (in_array($oneforall_mod_name, $modules))
                RssFeedAll_OneForAll( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $oneforall_mod_names, $wb ); 
        }

        if (in_array('procalendar', $modules))
            RssFeedAll_procalendar( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb );  

        /* another module xxxxxxxxx
        if (in_array('xxxxxxxx', $modules)) 
            RssFeedAll_xxxxxxxx( $output_array, $debug_info, $public, $counter, $RssFeedAll_exclude, $wb ); 
        */
        
        // sort
        $alastchange = array();

        foreach($output_array as $key => $entry){
                $alastchange[$key]  = $entry["lastchange"];
        }

        array_multisort($alastchange, SORT_DESC, $output_array);

        // output header
        RssFeedAll_Header($debug, $charset);

        // output list
        RssFeedAll_Items($output_array, $RssFeedAll_limit);
        
        // output footer
        RssFeedAll_footer( $debug, $counter, $page_counter, $debug_info);
    }

// merge configuration arrays
// **************************

    function RssFeedAll_MergeArrays($orig_arr, $diff_arr){ 
        $add_items = array();
        $del_items = array();
        foreach ( $diff_arr as $i ) {
            if(is_int($i)){
                if($i < 0) {
                    $del_items[] = -$i;
                } else {
                    $add_items[] = $i;
                }
            } else {
                if(substr($i, 0, 1) == '-') {
                    $del_items[] = substr($i, 1);
                } else {
                    $add_items[] = $i;
                }
            }
        }
        $tmp_arr = array_diff($orig_arr, $del_items);
        return array_merge($tmp_arr, $add_items);
    }


// Start with xml header output
// ****************************

    function RssFeedAll_Header($debug, $charset){

        if ($debug) {
            echo '<!DOCTYPE html>';
            echo "\n".'<head>';
            echo "\n\t".'<meta charset="'.$charset.'" />';
            echo "\n\t".'<style>';
            echo "\n\t".'    url {';
            echo "\n\t".'        display: block;';
            echo "\n\t".'    }';
            echo "\n\t".'    loc {';
            echo "\n\t".'        font-weight: bold;';
            echo "\n\t".'        line-height: 25px;';
            echo "\n\t".'    }';
            echo "\n\t".'</style>';
            echo "\n\t".'</head>';
        } else {

            // Sending XML header
            header("Content-type: text/xml; charset=$charset" );

            // Header info
            // Required by CSS 2.0
            echo '<?xml version="1.0" encoding="'.$charset.'"?>';
            echo "\n".'<rss version="2.0">';
            echo "\n".'<channel>'; 
            echo "\n\t".'<title><![CDATA['.WEBSITE_TITLE.']]></title>';    
            echo "\n\t".'<link>'.WB_URL.'</link>';   
            echo "\n\t".'<description>'.WEBSITE_DESCRIPTION.'</description>'; 
            // Optional header info 

            echo "\n\t".'<language>'.strtolower(DEFAULT_LANGUAGE).'</language>';
            echo "\n\t".'<copyright>Copyright '.date('Y').', '.WEBSITE_TITLE.'</copyright>';
            echo "\n\t".'<managingEditor>'. SERVER_EMAIL.'</managingEditor>';
            echo "\n\t".'<webMaster>'. SERVER_EMAIL.'</webMaster>';
            echo "\n\t".'<category>'. WEBSITE_TITLE.'</category>';
            echo "\n\t".'<generator>WebsiteBaker Content Management System</generator>';
        }

    }


// output of the individual entries
// ********************************

    function RssFeedAll_Items($output_array, $RssFeedAll_limit){
        $output_counter=0;
        foreach ($output_array as $o){
            global $shorturl;
            $link=$o['link'];
            if($shorturl) {
                    $linkstart = strlen(WB_URL.PAGES_DIRECTORY);
                    $linkend = strlen(PAGE_EXTENSION);
                    $link = WB_URL.substr( $link , $linkstart );
                    if(substr( $link , 0, -$linkend ) == PAGE_EXTENSION) {
                            $link = substr( $link , 0, -$linkend ).'/';
                    } else {
                            $link = str_replace( PAGE_EXTENSION , '/', $link);
                    }
            }
            $lin = '<item>'; 
            $lin .=  "\n\t\t".'<title><![CDATA['.$o['title']
                 .' / '.$o['lastchange'].']]></title>'; 
            $lin .=  "\n\t\t".'<link>'.$link.'</link>'; 
            $lin .=  "\n\t\t".'<description><![CDATA['.$o['title'].': '.$o['description'].']]></description>'; 
            $lin .=  "\n\t\t".'<category>'.$o['category'].'</category>'; 
            $lin .=  "\n\t\t".'<author>'.$o['author'].'</author>'; 
            $lin .=  "\n\t\t".'<pubDate>'.$o['pubDate'].'</pubDate>'; 
            $lin .=  "\n\t\t".'<guid>'.$link.'</guid>'; 
            $lin .=  "\n\t".'</item>';
            echo $lin;
            $output_counter++;
            if(($output_counter>=$RssFeedAll_limit) and ($RssFeedAll_limit!=0)) return;
        }
    }


    function RssFeedAll_footer(
        $debug,
        $counter,
        $page_counter,
        $debug_info
    ){

        // Debug
        if ($debug) {
                echo "\n".'<div style="display: block; white-space: pre; border: 2px solid #c77; padding: 0 1em 1em 1em; margin: 1em; line-height: 18px; background-color: #fdd; color: black">';
                echo "\n".'<h3>DEBUG</h3>';
                echo "\n".'<h3>Number of Pages</h3>';
                echo "\n".'<div style="font-family:monospace;font-size:12px">Number of Pages excluding module pages: '.$page_counter.'<br>';
                echo "\n".'Number of all Pages including module pages: '.$counter.'</div>';
                if (count($debug_info > 0)) {
                        echo "\n".'<h3>Banned Pages</h3><div style="font-family:monospace;font-size:12px">'.implode('', $debug_info).'</div>';
                }
                echo "\n".'</div>';
        } else {
            echo "\n".'</channel>';
            echo "\n".'</rss>';        
        }
    }


    function RssFeedAll_GetUserName ($userId) {
        global $database;
        $result = $database->query(
            "SELECT display_name "
                . "FROM " . TABLE_PREFIX . "users "
                . "WHERE `user_id` = $userId"
        );
        if ($result && $result->numRows() == 1) {
                $udata = $result->fetchRow();
                return $udata['display_name'];
        } else {
                return "unknown";
        }
    }


// Function RssFeedAll_check_link

    function RssFeedAll_check_link($link, $RssFeedAll_exclude) {
        static $listed = array();

        // Check for unwanted words in the url
        foreach ($RssFeedAll_exclude as $value) {
            if (strpos($link, $value)) {
                $unwanted = "&quot;$link&quot; contains &quot;$value&quot; and will not show up in the rss feed\n";
                return $unwanted;
            }
        }

        // External links should not show up as contents of the local rss feed
        if (strpos($link, '://')) {
            $unwanted = "$link is an external link and will not show up in the rss feed\n";
            return $unwanted;
        }
        if (in_array($link , $listed)) {
            $unwanted = "$link is already listed\n";
            return $unwanted;
        }

        if (trim($link) === '') {
            $unwanted = "Skipped empty link\n";
            return $unwanted;
        }

        $listed[] = $link;
        return true;
    }



// Get public WB pages

// *******************

    function RssFeedAll_Pages(
        &$output_array,
        &$debug_info,
        &$public,
        &$modules,
        &$counter,
        $RssFeedAll_exclude,
        $RssFeedAll_include,
        $wb
    ){

        $ts = time();

        // Get all pages from db except of the module menu_link
        $sql = "SELECT p.`link`,"
             . " p.`modified_when`,"
             . " p.`parent`,"
             . " p.`page_title`,"
             . " p.`position`,"
             . " p.`description`,"
             . " p.`visibility`,"
             . " p.`viewing_groups`,"
             . " p.`viewing_users`,"
             . " p.`page_id`,"
             . " p.`modified_by`,"
             . " s.`section_id`,"
             . " s.`module`"
             . " FROM `".TABLE_PREFIX."pages` p"
             . " JOIN `".TABLE_PREFIX."sections` s"
             . " ON p.`page_id` = s.`page_id`"
             . " WHERE  (s.`module` != 'menu_link')"
             . "    AND (s.`publ_start` = '0' OR s.`publ_start` <= $ts)"
             . "    AND (s.`publ_end` = '0' OR s.`publ_end` >= $ts)"
             . " ORDER BY p.`parent`, p.`position` ASC";

        global $database;
        $result = $database->query($sql);

        $category='Sitepages';

        // Loop through the pages
        if ($result && $result->numRows() > 0) {
            while ($page = $result->fetchRow()) {
                $curr_page_id = $page['page_id'];
                if(($wb->page_is_visible($page)) 
                    or (in_array($curr_page_id, $RssFeedAll_include)) 
                    or (in_array($page['visibility'], $RssFeedAll_include))
                    or (in_array('all', $RssFeedAll_include))
                ) {
                    $checked = RssFeedAll_check_link($page['link'], $RssFeedAll_exclude);
                    if ($checked === true) {
                        $ptitle = $page['page_title'];
                        $lastchange =  gmdate("Y-m-d", $page['modified_when']+TIMEZONE);
                        $pubDate = gmdate("r", $page['modified_when']+TIMEZONE);
                        $url = htmlspecialchars($wb->page_link($page['link']));
                        $record = array (
                            'lastchange' => $lastchange,
                            'title' => $ptitle,
                            'link'  => $url,  
                            'description' => $page['description'], 
                            'category' => $category, 
                            'author' => RssFeedAll_GetUserName($page['modified_by']), 
                            'pubDate' => $pubDate
                        );
                        $output_array[] = $record;
                        $counter++;
                        $public[]  = $page['section_id'];
                        $modules[] = $page['module'];
                    } else {
                        $debug_info[] = $checked;
                    }
                } else {
                    $debug_info[] = "$curr_page_id is not public and not listed in explicit include list\n";
                }
            }
        }
    }




// Get module pages of previously set modules
// ******************************************

    function RssFeedAll_News(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        $ts = time();
    
        // News
        $sql = "SELECT `section_id`,"
             . "       `link`,"
             . "       `posted_when`,"
             . "       `published_when`,"
             . "       `posted_by`,"
             . "       `content_short`,"
             . "       `title`"
             . " FROM `".TABLE_PREFIX."mod_news_posts`"
             . " WHERE `active` = '1'"
             . "   AND (`published_when` = '0' OR `published_when` <= $ts)"
             . "   AND (`published_until` = '0' OR `published_until` >= $ts)";
        global $database;
        $rs_news = $database->query($sql);

        $category='News';

        if ($rs_news->numRows() > 0) {
            while ($news = $rs_news->fetchRow()) {
                if (!in_array($news['section_id'], $public)) continue;
                $checked = RssFeedAll_check_link($news['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    $ptitle = $news['title'];
                    $lastchange =  gmdate("Y-m-d", $news['published_when']+TIMEZONE);
                    $pubDate = gmdate("r", $news['published_when']+TIMEZONE);
                    $url = htmlspecialchars($wb->page_link($news['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $url,  
                        'description' => $news['content_short'], 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($news['posted_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;

                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }

// Bakery
// ******

    function RssFeedAll_Bakery(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        $sql = "SELECT `section_id`,"
             . "       `link`,"
             . "       `modified_when`,"
             . "       `modified_by`,"
             . "       `created_when`,"
             . "       `created_by`,"
             . "       `description`,"
             . "       `title`"
             . " FROM `".TABLE_PREFIX."mod_bakery_items`"
             . " WHERE `active` = '1'";

        $category='Shopitems';

        global $database;
        $rs_bakery = $database->query($sql);
        if ($rs_bakery->numRows() > 0) {
            while ($bakery = $rs_bakery->fetchRow()) {
                if (!in_array($bakery['section_id'], $public)) continue;
                $checked = RssFeedAll_check_link($bakery['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    $ptitle = $bakery['title'];
                    $lastchange =  gmdate("Y-m-d", $bakery['modified_when']+TIMEZONE);
                    $pubDate = gmdate("r", $bakery['created_when']+TIMEZONE);
                    $url = htmlspecialchars($wb->page_link($bakery['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $url,  
                        'description' => $bakery['description'], 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($bakery['created_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }

// Catalog
// *******

    function RssFeedAll_Catalog(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        $sql = "SELECT `section_id`,"
             . "       `link`,"
             . "       `modified_when`,"
             . "       `modified_by`,"
             . "       `description`,"
             . "       `title`"
             . " FROM `".TABLE_PREFIX."mod_catalogs_list`"
             . " WHERE `active` = '1'";

        $category='Catalogitems';

        global $database;
        $rs_catalogs = $database->query($sql);
        if ($rs_catalogs->numRows() > 0) {
            while ($catalogs = $rs_catalogs->fetchRow()) {
                if (!in_array($catalogs['section_id'], $public)) continue;
                if ($checked === true) {
                    $ptitle = $catalogs['title'];
                    $lastchange = gmdate("Y-m-d", $catalogs['modified_when']+TIMEZONE);
                    $pubDate = gmdate("r", $catalogs['modified_when']+TIMEZONE);
                    $url = htmlspecialchars($wb->page_link($catalogs['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $url,  
                        'description' => $catalogs['description'], 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($catalogs['modified_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }

// Portfolio
// *********

    function RssFeedAll_Portfolio(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        $sql = "SELECT p.`link`, p.`position`, p.`modified_when`, s.`section_id`, p.`page_title`, p.`modified_by`"
             . " FROM `".TABLE_PREFIX."sections` s"
             . " JOIN `".TABLE_PREFIX."pages` p"
             . "   ON s.`page_id` = p.`page_id`"
             . " WHERE s.`module` = 'portfolio_detail'"
             . "   AND p.`position` > '1'"
             . " ORDER BY p.`parent`, p.`position` ASC;";

        $category='Portfolioitems';

        global $database;
        $rs_portfolio = $database->query($sql);
        if ($rs_portfolio->numRows() > 0) {
            while ($portfolio = $rs_portfolio->fetchRow()) {
                $checked = RssFeedAll_check_link($portfolio['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    $ptitle = $portfolio['title'];
                    $lastchange = gmdate("Y-m-d", $portfolio['modified_when']+TIMEZONE);
                    $pubDate = gmdate("r", $portfolio['modified_when']+TIMEZONE);
                    $length  = strrpos($portfolio['link'], '/');
                    $link    = substr($portfolio['link'], 0, $length);
                    $link    = htmlspecialchars($wb->page_link($link)).'?item='.$portfolio['position'];
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $link,  
                        'description' => $ptitle, 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($portfolio['modified_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }

// Topics
// ******

    function RssFeedAll_Topics(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $topics_mod_name,
        $RssFeedAll_exclude, 
        $wb
    ){

        require(WB_PATH.'/modules/'.$topics_mod_name.'/module_settings.php');
        $t = mktime ( (int) gmdate("H"), (int) gmdate("i"), (int) gmdate("s"), (int) gmdate("n"), (int) gmdate("j"), (int) gmdate("Y")) + DEFAULT_TIMEZONE;
        $sql = "SELECT `section_id`,"
             . "       `link`,"
             . "       `posted_modified`,"
             . "       `posted_by`,"
             . "       `description`,"                 
             . "       `posted_first`,"
             . "       `title`"
             . "  FROM `".TABLE_PREFIX."mod_".$topics_mod_name."`"
             . " WHERE (`active` > '3' OR `active` = '1')"
             . "   AND (`published_when` = '0' OR `published_when` < ".$t.")"
             . "   AND (`published_until` = '0' OR `published_until` > ".$t.")"
             . " ORDER BY `position` DESC";

        $category='Topicsposts';

        global $database;
        $rs_topics = $database->query($sql);
        if($rs_topics->numRows() > 0) {
            while($topics = $rs_topics->fetchRow()) {
                if (!in_array($topics['section_id'], $public)) continue;
                $checked = RssFeedAll_check_link($topics['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    $ptitle = $topics['title'];
                    $lastmod = gmdate("Y-m-d", $topics['posted_modified']+TIMEZONE);
                    $pubDate = gmdate("r", $topics['posted_first']+TIMEZONE);
                    $link    = htmlspecialchars(WB_URL.$topics_directory.$topics['link'].PAGE_EXTENSION);
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $link,  
                        'description' => $topics['description'], 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($topics['posted_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }

// Showcase
// ********

    function RssFeedAll_Showcase(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        $sql = "SELECT `section_id`,"
             . "       `link`,"
             . "       `modified_when`"
             . "  FROM `".TABLE_PREFIX."mod_showcase_items`"
             . " WHERE `active` = '1'";

        $category='Showcaseitems';

        global $database;
        $rs_showcase = $database->query($sql);
        if($rs_showcase->numRows() > 0) {
            while($showcase = $rs_showcase->fetchRow()) {
                if (!in_array($showcase['section_id'], $public)) continue;
                $checked = RssFeedAll_check_link($showcase['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    if (!empty($showcase['link'])) {
                        $path = $database->get_one("SELECT `link` FROM `".TABLE_PREFIX."pages` WHERE `page_id` = (SELECT `page_id` FROM `".TABLE_PREFIX."mod_showcase_items` WHERE `link` = '".$showcase['link']."' LIMIT 1);");
                        $showcase['link'] = $path.$showcase['link'];
                    }
                    $ptitle = 'showcase';
                    $lastmod = gmdate("Y-m-d", $showcase['modified_when']+TIMEZONE);
                    $pubDate = gmdate("r", $showcase['modified_when']+TIMEZONE);
                    $link    = htmlspecialchars($wb->page_link($showcase['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $link,  
                        'description' => $ptitle, 
                        'category' => $category, 
                        'author' => '', 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }

// OneForAll
// *********

    function RssFeedAll_OneForAll(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $oneforall_mod_name,
        $wb
    ){

        $sql = "SELECT `section_id`,"
             . "       `page_id`,"
             . "       `link`,"
             . "       `modified_when`,"
             . "       `title`,"
             . "       `modified_by`"
             . "  FROM `".TABLE_PREFIX."mod_".$oneforall_mod_name."_items`"
             . " WHERE `active` = '1'";

        $category=$oneforall_mod_name.'items';

        global $database;
        $rs_oneforall = $database->query($sql);
        if($rs_oneforall->numRows() > 0) {
            while($oneforall = $rs_oneforall->fetchRow()) {
                if (!in_array($oneforall['section_id'], $public)) continue;
                $page = $database->get_one("SELECT `link` FROM `".TABLE_PREFIX."pages` WHERE `page_id`='".$oneforall['page_id']."'");
                $checked = RssFeedAll_check_link($page.$oneforall['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    $ptitle = $oneforall['title'];
                    $lastmod = gmdate("Y-m-d", $oneforall['modified_when']+TIMEZONE);
                    $pubDate = gmdate("r", $oneforall['modified_when']+TIMEZONE);
                    $link    = htmlspecialchars($wb->page_link($page.$oneforall['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $link,  
                        'description' => $ptitle, 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($oneforall['posted_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                }
                else {
                    $debug_info[] = $checked;
                }
            }
        }
    }




// procalendar
// ***********


    function RssFeedAll_procalendar(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        // Set defaults
        date_default_timezone_set('UTC');
        $year  = date('Y', time()); 
        $month = date('n', time());

        // Editable values
        // Show how many items, defaults to 10?
        $max   = 10; 

        // Set time frame for coming events, default one year
        $year2 = $year + 1;
        $month2 = $month;

        // Get items from database

        // Set start- and end date for query
        $datestart = "$year-$month-1";
        $dateend = "$year2-$month2-".cal_days_in_month(CAL_GREGORIAN, $month2,$year2);


        $sql = "SELECT `section_id`,"
             . "       `page_id`,"
             . "       `id`,"
             . "       `date_start`,"
             . "       `time_start`,"
             . "       `name`,"
             . "       `description`,"
             . "       `owner`"
             . "  FROM `".TABLE_PREFIX."mod_procalendar_actions`"
             . " WHERE date_start <='$dateend' AND date_end >='$datestart' AND public_stat = 0 "
             . " ORDER BY date_start,time_start LIMIT 0, ".$max." ";


        $category='calendar';

        global $database;
        $rs_procalendar = $database->query($sql);
        if ($rs_procalendar->numRows() > 0) {
            while ($procalendar = $rs_procalendar->fetchRow()) {
                $page_link="http://www.example.com";
                $page_id=$procalendar['page_id'];
                // Get page link, needed for linkage
                if ($page_id <> 0) {
                   $sql = "SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".$page_id."'";
                   $result = $database->query($sql);
                   if ( $result->numRows() > 0 ) {
                      while( $row = $result->fetchRow() ) {
                         $page_link = $wb->page_link($row['link']);
                      }
                   }
                }                    
                $link = $page_link.'?id='.$procalendar['id'].'&amp;detail=1';

                $checked = RssFeedAll_check_link($procalendar['link'], $RssFeedAll_exclude);
                if ($checked === true) {                    
                    $ptitle = $procalendar['name'];
                    $lastchange = gmdate("Y-m-d", $procalendar['date_start']);
                    $pubDate = gmdate("r", $procalendar['date_start']);
                    $link = htmlspecialchars($wb->page_link($procalendar['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $link,  
                        'description' => stripslashes($procalendar["description"]), 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($procalendar['owner']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }





// Sample Module
// *************
//
// Add another module here...
// Example code
/*


    function RssFeedAll_xxxxxxxx(
        &$output_array,
        &$debug_info,
        &$public,
        &$counter,
        $RssFeedAll_exclude, 
        $wb
    ){

        $sql = "SELECT `section_id`,"
             . "       `link`,"
             . "       `modified_when`,"
             . "       `created_when`,"
             . "       `title`,"
             . "       `created_by`"
             . "  FROM `".TABLE_PREFIX."mod_xxxxxxxx_items`"
             . " WHERE `active` = '1'";

        $category='xxxxxxxxitems';

        global $database;
        $rs_xxxxxxxx = $database->query($sql);
        if ($rs_xxxxxxxx->numRows() > 0) {
            while ($xxxxxxxx = $rs_xxxxxxxx->fetchRow()) {
                $checked = RssFeedAll_check_link($xxxxxxxx['link'], $RssFeedAll_exclude);
                if ($checked === true) {
                    $ptitle = $xxxxxxxx['title'];
                    $lastchange = gmdate("Y-m-d", $xxxxxxxx['modified_when']+TIMEZONE);
                    $pubDate = gmdate("r", $xxxxxxxx['created_when']+TIMEZONE);
                    $link = htmlspecialchars($wb->page_link($xxxxxxxx['link']));
                    $record = array (
                        'lastchange' => $lastchange,
                        'title' => $ptitle,
                        'link'  => $link,  
                        'description' => $ptitle, 
                        'category' => $category, 
                        'author' => RssFeedAll_GetUserName($xxxxxxxx['created_by']), 
                        'pubDate' => $pubDate
                    );
                    $output_array[] = $record;
                    $counter++;
                } else {
                    $debug_info[] = $checked;
                }
            }
        }
    }
*/

}
