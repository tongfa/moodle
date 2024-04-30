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
 * Version metadata for the local_easyappointments plugin.
 *
 * @package   local_easyappointments
 * @copyright 2024, Chris David <eaplugin@sky.chrisdavid.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class external_url {
    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }

    public function __toString() {
        return $this->url;
    }
}

function local_easyappointments_before_http_headers() {
    global $CFG, $PAGE;
    if (isloggedin() && !isguestuser()) {
        $PAGE->primarynav->add(get_string('lessons', 'local_easyappointments'), new external_url('/book/index.php/backend'), navigation_node::TYPE_ROOTNODE, 'easyappointments-schedule');
    }
}
