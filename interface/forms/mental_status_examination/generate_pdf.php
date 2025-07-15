<?php
require_once("../../globals.php");
require_once("$srcdir/api.inc.php");
require_once($GLOBALS['fileroot'] . "/vendor/autoload.php");

use Dompdf\Dompdf;

// Fetch your form data
$id = $_GET['id'] ?? null;
$data = formFetch("form_mental_status", $id);

if (!$data) {
    die("Invalid ID or no data found.");
}

// Fetch patient details
$pid = $data['pid'] ?? null;
$encounter = $data['encounter'] ?? null;

$patient = sqlQuery("SELECT fname, lname, pubpid FROM patient_data WHERE pid = ?", [$pid]);
$encounterData = sqlQuery("SELECT date FROM form_encounter WHERE encounter = ? AND pid = ?", [$encounter, $pid]);

$patientName = $patient ? $patient['fname'] . " " . $patient['lname'] : "Unknown Patient";
$patientId = $patient['pubpid'] ?? '';
$encounterDate = $encounterData['date'] ?? '';

$html = "
    <h2 style='text-align:center;'>Mental Status Examination</h2>
    <p><strong>Patient Name:</strong> {$patientName}</p>
    <p><strong>Patient ID:</strong> {$patientId}</p>
    <p><strong>Encounter Date:</strong> {$encounterDate}</p>
    <br>
    <table border='1' cellspacing='0' cellpadding='5'>
";

// Add MSE data to table
foreach ($data as $key => $value) {
    if (in_array($key, ['id', 'form_id', 'pid', 'encounter', 'user', 'groupname', 'authorized', 'activity', 'date', 'uuid'])) {
        continue;
    }

    if ($value === "" || $value === "0000-00-00 00:00:00") {
        continue;
    }

    $label = ucwords(str_replace("_", " ", $key));

    if (is_string($value) && strpos($value, ',') !== false) {
        $value = str_replace(",", ", ", $value);
    }

    $html .= "<tr><td><strong>$label</strong></td><td>$value</td></tr>";
}

$html .= "</table>";

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output to browser
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"mental_status.pdf\"");

echo $dompdf->output();
exit;
