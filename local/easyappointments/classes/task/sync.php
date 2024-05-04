<?php
/**
 * Version metadata for the local_easyappointments plugin.
 *
 * @package   local_easyappointments
 * @copyright 2024, Chris David <eaplugin@sky.chrisdavid.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_easyappointments\task;

require_once(__DIR__.'/../../../../calendar/lib.php');

class sync extends \core\task\scheduled_task {
    public function get_name() {
        // Shown in admin screens
        return get_string('Easy!Appointment sync', 'local_easyappointments');
    }

    public function execute() {
        mtrace("*local******************************************* STARTING");
        //$this->check_for_appointments();
        // Code executed when the cron runs this task
        mtrace("*local******************************************* DONE");
    }

    private function create_calendar_event($name, $description, $user_id, $timestart, $timeend) {
        // Convert datetime strings to UNIX timestamps
        $start_timestamp = strtotime($timestart);
        $end_timestamp = strtotime($timeend);
        $duration_seconds = $end_timestamp - $start_timestamp;

        $event = new stdClass();
        $event->name = $name;
        $event->description = $description;
        $event->courseid = 0;
        $event->groupid = 0;
        $event->userid = $user_id;
        $event->modulename = 0;
        $event->instance = 0;
        $event->eventtype = 'user';
        $event->timestart = $start_timestamp;
        $event->timeduration = $duration_seconds;
        $event->visible = 1;
        $event->sequence = 1;

        $created_event = calendar_event::create($event);
        return $created_event;

        // if ($created_event !== false) {
        //     echo 'Event created successfully. Event ID: ' . $created_event->id;
        // } else {
        //     echo 'Failed to create the event.';
        // }
    }

    private function check_for_appointments() {
        global $DB;

        $sql = "SELECT MAX(easyappointments_id) AS max_id FROM {ea_sync}";

        $maxId = $DB->get_field_sql($sql);
        $maxId = $maxId || 0;

        // SQL statement to fetch records with ID greater than $maxId
        $sql = "select start_datetime, end_datetime, notes, ".
            "p.firstname as p_firstname, p.lastname as p_lastname, ".
            "c.firstname as c_firstname, c.lastname as c_lastname, ".
            "c.id as c_id, p.id as p_id, a.id as ea_id ".
            "from ea_appointments a inner join mdl_user p on a.id_users_provider = p.id inner join mdl_user c on a.id_users_customer = c.id ".
            "where a.id > ?";

        // Execute the query and fetch records
        $params = [$maxId]; // Parameters for the SQL statement
        $records = $DB->get_records_sql($sql, $params);

        if (!$records) {
            mtrace("There are no outstanding Easy!Appointments records");
            return;
        }

        foreach ($records as $r) {
            $provider_name = "{$r->p_firstname} {$r->p_lastname}";
            $customer_name = "{$r->c_firstname} {$r->c_lastname}";

            // for provider
            $provider_event_id = $this->create_calendar_event(
                "Lesson for {$customer_name}",
                $r->notes,
                $r->p_id,
                $r->start_datetime,
                $r->end_datetime,
            );

            // for customer
            $customer_event_id = $this->create_calendar_event(
                "Lesson with {$provider_name}",
                $r->notes,
                $r->c_id,
                $r->start_datetime,
                $r->end_datetime,
            );

            $sql = "INSERT INTO {ea_sync} (easyappointments_id, provider_calendar_id, student_calendar_id)
            VALUES (?, ?, ?)";
            $params = [$r->ea_id, $provider_event_id, $customer_event_id];

            try {
                // Execute the query with parameters
                $DB->execute($sql, $params);
                mtrace("Data inserted successfully");
            } catch (dml_exception $e) {
                // Handle the exception if something goes wrong
                mtrace("Failed to sync appointment: " . $e->getMessage());
            }
        }
        mtrace("*local****************************************** Completed step 1 of sync");
    }
}
