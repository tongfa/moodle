<?php
// Define CLI_SCRIPT constant
define('CLI_SCRIPT', true);

// Require Moodle config
require(__DIR__ . '/classes/task/sync.php');

// Ensure the script is being run from the command line, not a web interface
if (PHP_SAPI !== 'cli') {
    die('This script must be run from the command line.');
}

// Your CLI code logic here
echo "Starting Moodle Easy!Appointments CLI script.\n";

// Override mtrace to use echo
// mtrace('', function($string) {
//     echo $string;
// });

$task = new \local_easyappointments\task\sync();  // Replace with your task's class
$task->execute();

// For example, a simple task execution or database update
