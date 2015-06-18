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
 * Math filter admin settings
 *
 * @package    local_math
 * @copyright  Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('filtersettings', new admin_category('filtermath', get_string('generators', 'local_math'), false));

$settings = new admin_settingpage('filtersettingmath', new lang_string('settings', 'local_math'));
if ($ADMIN->fulltree) {
    require_once(__DIR__.'/adminlib.php');
    // $settings->add(new admin_setting_heading('mathgeneralheader', new lang_string('settings'), ''));
    $settings->add(new math_subplugins_settings());

}
$ADMIN->add('filtermath', $settings);
unset($settings);

foreach (core_plugin_manager::instance()->get_plugins_of_type('math') as $plugin) {
    /* @var \local_math\plugininfo\math $plugin */
    $plugin->load_settings($ADMIN, 'filtermath', $hassiteconfig);
}

// Math filter does not have standard settings page.
$settings = null;
