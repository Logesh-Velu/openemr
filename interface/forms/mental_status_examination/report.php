<?php

/**
 * Mental Status Examination - Report
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Logesh
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once(dirname(__FILE__) . '/../../globals.php');
require_once($GLOBALS["srcdir"] . "/api.inc.php");

function mental_status_examination_report($pid, $encounter, $cols, $id)
{
    $count = 0;
    $data = formFetch("form_mental_status", $id);

    // Check if form data is valid
    if (!$data || !is_array($data)) {
        echo "<p><b>" . xlt("No data found for this form.") . "</b></p>";
        return;
    }

    // Remove keys we don't want to display
    unset($data['form_id']);
    unset($data['uuid']);
    unset($data['encounter']);
    unset($data['id']);
    unset($data['pid']);
    unset($data['user']);
    unset($data['groupname']);
    unset($data['authorized']);
    unset($data['activity']);
    unset($data['date']);

    echo "<table><tr>";

    foreach ($data as $key => $value) {
        if ($value === "" || $value === "0000-00-00 00:00:00") {
            continue;
        }

        // Convert checkboxes or multi-selects
        if ($value === "on") {
            $value = "yes";
        }

        if (is_string($value) && strpos($value, ',') !== false) {
            $value = str_replace(",", ", ", $value);
        }

        // Convert field name to readable label
        $label = ucwords(str_replace("_", " ", $key));

        echo "<td><span class='bold'>" . xlt($label) . ": </span><span class='text'>" . text($value) . "</span></td>";
        $count++;

        if ($count == $cols) {
            $count = 0;
            echo "</tr><tr>";
        }
    }

    echo "</tr></table>";
}
