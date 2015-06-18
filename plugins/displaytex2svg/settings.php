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
 *  dvisvgm integration settings.
 *
 * @package    local_math
 * @subpackage displaytex2svg
 * @copyright  2014 onward Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$defaultpathlatex = '/usr/bin/latex';
$defaultpathdvips = '/usr/bin/dvips';
$defaultpathdvisvgm = '/usr/bin/dvisvgm';
$defaultdelimiters = '[["\\\\[", "\\\\]"], ["$$", "$$"], ["d%", "%d"]]';
$defaultscale = 1000;

$plugin = local_math_plugin::get('displaytex2svg');

$image = $plugin->get_image_url(get_config('math_displaytex2svg', 'testexp') . "\\phantom{" .
    get_config('math_displaytex2svg', 'scale') . "}");

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('math_displaytex2svg/delimiters',
        get_string('delimiters', 'local_math'),
        get_string('delimiters_desc', 'local_math'), $defaultdelimiters));
    $settings->add(new admin_setting_configexecutable('filter_tex/pathlatex',
        get_string('pathlatex', 'filter_tex'), '', $defaultpathlatex));
    $settings->add(new admin_setting_configexecutable('filter_tex/pathdvips',
        get_string('pathdvips', 'filter_tex'), '', $defaultpathdvips));
    $settings->add(new admin_setting_configexecutable('filter_math/pathdvisvgm',
        get_string('pathdvisvgm', 'math_displaytex2svg'), '', $defaultpathdvisvgm));
    $settings->add(new admin_setting_configtext('math_displaytex2svg/scale',
        get_string('scale', 'math_displaytex2svg'),
        get_string('scale_desc', 'math_displaytex2svg'), $defaultscale));
    $settings->add(new admin_setting_configtext('math_displaytex2svg/testexp',
        get_string('testexp', 'math_displaytex2svg'),
        get_string('testexp_desc', 'math_displaytex2svg') . "<p><a href=\"$image\"><img src=\"$image\" alt=\"" .
        get_config('math_displaytex2svg', 'testexp') . "\" ></a></p>", '\\sum^\\infty_{k=1}\\frac 1{k^2}'));

}
