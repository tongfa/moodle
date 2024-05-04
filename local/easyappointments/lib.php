<?php
/**
 * Page hook for the local_easyappointments plugin.
 *
 * @package   local_easyappointments
 * @copyright 2024, Chris David <eaplugin@sky.chrisdavid.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', true);
defined('MOODLE_INTERNAL') || die();

require(__DIR__ . '/../../config.php');

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
