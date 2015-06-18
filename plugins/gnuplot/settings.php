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
 * Gnuplot integration settings.
 *
 * @package   math_gnuplot
 * @copyright 2014 Daniel Thies <dthies@ccal.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$defaultdelimiters = '[["g%", "%g"]]';
$defaultpathgnuplot = '/usr/bin/gnuplot';

$plugin = local_math_plugin::get('gnuplot');
$image = $plugin->get_image_url(get_config('math_gnuplot', 'testexp'));


if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('math_gnuplot/delimiters',
        get_string('delimiters', 'local_math'),
        get_string('delimiters_desc', 'local_math'),
        $defaultdelimiters));
    $settings->add(new admin_setting_configexecutable('local_math/pathgnuplot',
        get_string('pathgnuplot', 'local_math'), '', $defaultpathgnuplot));
    $settings->add(new admin_setting_configtext('math_gnuplot/imgheight',
        get_string('imgheight', 'math_gnuplot'), '', 240));
    $settings->add(new admin_setting_configtext('math_gnuplot/imgwidth',
        get_string('imgwidth', 'math_gnuplot'), '', 320));
    $settings->add(new admin_setting_configtext('math_gnuplot/testexp',
        get_string('testexp', 'local_math'),
        get_string('testexp_desc', 'local_math') . "<p><a href=\"$image\"><img src=\"$image\" alt=\"" .
        get_config('math_gnuplot', 'testexp') . "\" ></a></p>", 'plot x**2;'));

}
