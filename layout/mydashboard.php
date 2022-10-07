<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Boost Union - mydashboard page layout.
 *
 * This layoutfile is based on theme/boost/layout/mydashboard.php
 *
 *
 * @package   theme_boost_union
 * @copyright 2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @copyright based on code from theme_boost by Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Require own locallib.php.
require_once($CFG->dirroot . '/theme/boost_union/locallib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();
$topaddblockbutton = $OUTPUT->addblockbutton('outside-top');
$bottomaddblockbutton = $OUTPUT->addblockbutton('outside-bottom');
$leftaddblockbutton = $OUTPUT->addblockbutton('outside-left');
$rightaddblockbutton = $OUTPUT->addblockbutton('outside-right');
$fotterleftaddblockbutton = $OUTPUT->addblockbutton('footer-left');
$footerrightaddblockbutton = $OUTPUT->addblockbutton('footer-right');
$footercenteraddblockbutton = $OUTPUT->addblockbutton('footer-center');

user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$regions = [
    'left' => 'outside-left',
    'right' => 'outside-right',
    'top' => 'outside-top',
    'bottom' => 'outside-bottom',
    'footerleft' => 'footer-left',
    'footerright' => 'footer-right',
    'footercenter' => 'footer-center',
    'offcanvas' => 'off-canvas'
];
$regionsdata = [];
foreach ($regions as $name => $region) {

    if (!has_capability('theme/boost_union:viewregion'.$name, $PAGE->context)) {
        $regionsdata[$name] = ['hasblocks' => false];
        continue;
    }
    $regionhtml = $OUTPUT->blocks($region);
    $blockbutton = (has_capability('theme/boost_union:editregion'.$name, $PAGE->context)) ? $OUTPUT->addblockbutton($region) : '';
    $regionsdata[$name] = [
        'hasblocks' => (strpos($regionhtml, 'data-block=') !== false || !empty($blockbutton)),
        'regionhtml' => $regionhtml,
        'addblockbutton' => $blockbutton
    ];
}

if ((!empty($regionsdata['left']['hasblocks'])) && (!empty($regionsdata['right']['hasblocks']))) {
    $regionclass = 'main-content-region-block';
    $mainregionclass = 'main-region-block';
} else if (!empty($regionsdata['left']['hasblocks'])) {
    $regionclass = 'main-content-left-region';
} else if (!empty($regionsdata['right']['hasblocks'])) {
    $regionclass = 'main-content-right-region';
}

$footercount = $regionsdata['footerleft']['hasblocks'];
$footercount += $regionsdata['footerright']['hasblocks'];
$footercount += $regionsdata['footercenter']['hasblocks'];

if ($footercount == 1) {
    $footerclass = 'col-xl-12';
} else if ($footercount == 2) {
    $footerclass = 'col-xl-6';
} else if ($footercount == 3) {
    $footerclass = 'col-xl-4';
}


$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'regionclass' => isset($regionclass) ? $regionclass : '',
    'regionplacement' => get_config('theme_boost_union', 'regionplacement') == 0 ? 'blocks-next-maincontent' : 'blocks-near-window',
    'mainregionclass' => isset($mainregionclass) ? $mainregionclass : '',
    'footerclass' => isset($footerclass) ? $footerclass : ''
];
// Additional block regions.
$templatecontext['regions'] = (isset($regionsdata)) ? $regionsdata : [];
// Get and use the course related hints HTML code, if any hints are configured.
$courserelatedhintshtml = theme_boost_union_get_course_related_hints();
if ($courserelatedhintshtml) {
    $templatecontext['courserelatedhints'] = $courserelatedhintshtml;
}

// Include the template content for the course related hints.
require_once(__DIR__ . '/includes/courserelatedhints.php');

// Include the content for the back to top button.
require_once(__DIR__ . '/includes/backtotopbutton.php');

// Include the template content for the footnote.
require_once(__DIR__ . '/includes/footnote.php');

// Include the template content for the static pages.
require_once(__DIR__ . '/includes/staticpages.php');

// Include the template content for the JavaScript disabled hint.
require_once(__DIR__ . '/includes/javascriptdisabledhint.php');

// Include the template content for the info banners.
require_once(__DIR__ . '/includes/infobanners.php');


// Render drawers.mustache from boost_union.
echo $OUTPUT->render_from_template('theme_boost_union/mydashboard', $templatecontext);
