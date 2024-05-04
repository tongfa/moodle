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
 * Upgrade code for easyappointments integration
 *
 * @package   local_easyappointments
 * @copyright 2024, Chris David <eaplugin@sky.chrisdavid.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

 function xmldb_local_easyappointments_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024010104) {

        // Define table ea_sync to be created.
        $table = new xmldb_table('ea_sync');

        // Adding fields to table ea_sync.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('easyappointments_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('calendar_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table ea_sync.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table ea_sync.
        $table->add_index('easyappointments_id_idx', XMLDB_INDEX_UNIQUE, ['easyappointments_id']);
        $table->add_index('calendar_id_idx', XMLDB_INDEX_UNIQUE, ['calendar_id']);

        // Conditionally launch create table for ea_sync.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Easyappointments savepoint reached.
        upgrade_plugin_savepoint(true, 2024010104, 'local', 'easyappointments');

    }
    return true;
}
