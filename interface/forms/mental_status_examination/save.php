<?php

require_once("../../globals.php");
require_once("$srcdir/api.inc.php");
require_once("$srcdir/forms.inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"] ?? '')) {
    CsrfUtils::csrfNotVerified();
}

// Collect and sanitize all input data
$appearance = isset($_POST['appearance']) ? implode(", ", array_map('text', $_POST['appearance'])) : "";
$attention = text($_POST['attention'] ?? "");
$concentration = text($_POST['concentration'] ?? "");
$hallucinations = isset($_POST['hallucinations']) ? implode(", ", array_map('text', $_POST['hallucinations'])) : "";
$delusion = isset($_POST['delusion']) ? implode(", ", array_map('text', $_POST['delusion'])) : "";
$memory = isset($_POST['memory']) ? implode(", ", array_map('text', $_POST['memory'])) : "";
$intelligence = text($_POST['intelligence'] ?? "");
$orientation = isset($_POST['orientation']) ? implode(", ", array_map('text', $_POST['orientation'])) : "";
$social_judgement = text($_POST['social_judgement'] ?? "");
$insight = text($_POST['insight'] ?? "");
$thought_content = isset($_POST['thought_content']) ? implode(", ", array_map('text', $_POST['thought_content'])) : "";
$affect = text($_POST['affect'] ?? "");
$affect_description = text($_POST['affect_description'] ?? "");
$mood = text($_POST['mood'] ?? "");
$mood_description = text($_POST['mood_description'] ?? "");
$speech = isset($_POST['speech']) ? implode(", ", array_map('text', $_POST['speech'])) : "";
$behavior = isset($_POST['behavior']) ? implode(", ", array_map('text', $_POST['behavior'])) : "";
$thought_disorder = isset($_POST['thought_disorder']) ? implode(", ", array_map('text', $_POST['thought_disorder'])) : "";
$sleep = text($_POST['sleep'] ?? "");
$appetite = text($_POST['appetite'] ?? "");
$weight = isset($_POST['weight']) ? implode(", ", array_map('text', $_POST['weight'])) : "";
$eating_disorders = isset($_POST['eating_disorders']) ? implode(", ", array_map('text', $_POST['eating_disorders'])) : "";
$self_harm = isset($_POST['self_harm']) ? implode(", ", array_map('text', $_POST['self_harm'])) : "";

// Mode: new or update
$mode = $_GET['mode'] ?? 'new';
$form_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Get current patient/session info
$pid = $_SESSION['pid'] ?? null;
$encounter = $_SESSION['encounter'] ?? null;
$userauthorized = $_SESSION['userauthorized'] ?? null;
$activity = 1;

if (empty($pid) || empty($encounter)) {
    die(xlt("Invalid session data"));
}

// Save or update
if ($mode === 'new') {
    $data = [
        $pid, $activity, $encounter,
        $appearance, $attention, $concentration, $hallucinations, $delusion, $memory,
        $intelligence, $orientation, $social_judgement, $insight, $thought_content,
        $affect, $affect_description, $mood, $mood_description,
        $speech, $behavior, $thought_disorder, $sleep, $appetite, $weight,
        $eating_disorders, $self_harm, date("Y-m-d H:i:s")
    ];

    $newid = sqlInsert("INSERT INTO form_mental_status (
        pid, activity, encounter, appearance, attention, concentration, hallucinations, delusion, memory, intelligence,
        orientation, social_judgement, insight, thought_content, affect, affect_description, mood, mood_description,
        speech, behavior, thought_disorder, sleep, appetite, weight, eating_disorders, self_harm, date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $data);

    if ($newid) {
        addForm($encounter, "Mental Status", $newid, "mental_status_examination", $pid, $userauthorized);
    } else {
        die(xlt("Failed to save form"));
    }

} elseif ($mode === 'update' && $form_id) {
    $data = [
        $appearance, $attention, $concentration, $hallucinations, $delusion, $memory,
        $intelligence, $orientation, $social_judgement, $insight, $thought_content,
        $affect, $affect_description, $mood, $mood_description,
        $speech, $behavior, $thought_disorder, $sleep, $appetite, $weight,
        $eating_disorders, $self_harm,
        $form_id, $pid, $encounter
    ];

    $result = sqlStatement("UPDATE form_mental_status SET
        appearance = ?, attention = ?, concentration = ?, hallucinations = ?, delusion = ?, memory = ?, intelligence = ?,
        orientation = ?, social_judgement = ?, insight = ?, thought_content = ?, affect = ?, affect_description = ?,
        mood = ?, mood_description = ?, speech = ?, behavior = ?, thought_disorder = ?, sleep = ?, appetite = ?, weight = ?,
        eating_disorders = ?, self_harm = ?, date = NOW()
        WHERE id = ? AND pid = ? AND encounter = ?", $data);

    if (!$result) {
        die(xlt("Failed to update form"));
    }
}

// Redirect back
formHeader("Redirecting...");
formJump();
formFooter();
