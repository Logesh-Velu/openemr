<?php

/**
 * Clinical Notes form new.php Borrowed from Care Plan
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @author    Vinish K <vinish@zhservices.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @copyright Copyright (c) 2017-2019 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2021 <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2021 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../../globals.php");
require_once("$srcdir/api.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/patient.inc.php");
require_once("$srcdir/options.inc.php");
require_once($GLOBALS['srcdir'] . '/csv_like_join.php');

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Uuid\UuidRegistry;
use OpenEMR\Core\Header;
use OpenEMR\Services\MentalStatusExamination;

$returnurl = 'encounter_top.php';
$obj = formFetch("form_mental_status", $_GET["id"]);


function checkboxChecked($array, $value) {
    return in_array($value, $array ?? []) ? 'checked' : '';
}
function radioChecked($field, $value) {
    global $obj;
    return ($obj[$field] ?? '') === $value ? 'checked' : '';
}
function parseField($field) {
    return !empty($field)
        ? array_map('trim', explode(',', $field))
        : [];
}


// Use this to fetch checkbox data
$appearance = parseField($obj['appearance'] ?? '');
$hallucinations = parseField($obj['hallucinations'] ?? '');
$delusion = parseField($obj['delusion'] ?? '');
$memory = parseField($obj['memory'] ?? '');
$orientation = parseField($obj['orientation'] ?? '');
$social_judgement = parseField($obj['social_judgement'] ?? '');
$insight = parseField($obj['insight'] ?? '');
$thought_content = parseField($obj['thought_content'] ?? '');
$speech = parseField($obj['speech'] ?? '');
$behavior = parseField($obj['behavior'] ?? '');
$thought_disorder = parseField($obj['thought_disorder'] ?? '');
$weight = parseField($obj['weight'] ?? '');
$eating_disorders = parseField($obj['eating_disorders'] ?? '');
$self_harm = parseField($obj['self_harm'] ?? '');

// Radio/Single value fields don't need explode
$attention = $obj['attention'] ?? '';
$concentration = $obj['concentration'] ?? '';
$intelligence = $obj['intelligence'] ?? '';
$affect = $obj['affect'] ?? '';
$affect_description = $obj['affect_description'] ?? '';
$mood = $obj['mood'] ?? '';
$mood_description = $obj['mood_description'] ?? '';
$sleep = $obj['sleep'] ?? '';
$appetite = $obj['appetite'] ?? '';

?>
<html>
<head>
    <title><?php echo xlt("Mental Status Examination"); ?></title>

    <?php Header::setupHeader(['datetime-picker']); ?>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
       <h2 class="d-flex justify-content-between align-items-center">
                <span><?php echo xlt('Mental Status Examination'); ?></span>
                <a href="<?php echo $GLOBALS['rootdir']; ?>/forms/mental_status_examination/generate_pdf.php?id=<?php echo attr($_GET['id']); ?>" 
                target="_blank" class="btn btn-secondary btn-sm">
                <?php echo xlt('Download PDF'); ?>
                </a>
                </h2>

                <form method='post' name='my_form' action='<?php echo $rootdir ?>/forms/mental_status_examination/save.php?mode=update&id=<?php echo attr_url($_GET["id"] ?? ''); ?>'>
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="id" value="<?php echo attr($_GET["id"] ?? ''); ?>" />
                    <input type="hidden" name="formid" value="<?php echo attr($_GET["formid"] ?? ''); ?>" />
                 
                    <div class="container-fluid">
                        <div class="tb_row" id="tb_row">
                            <!-- Appearance -->
                            <fieldset>
                                <legend><?php echo xlt('Appearance'); ?></legend>
                                <label><input type="checkbox" name="appearance[]" value="Well-groomed"<?php echo checkboxChecked($appearance, "Well-groomed"); ?>> <?php echo xlt('Well-groomed'); ?></label>
                                <label><input type="checkbox" name="appearance[]" value="Disheveled"<?php echo checkboxChecked($appearance, "Disheveled"); ?>> <?php echo xlt('Disheveled'); ?></label>
                                <label><input type="checkbox" name="appearance[]" value="Bizarre"<?php echo checkboxChecked($appearance, "Bizarre"); ?>> <?php echo xlt('Bizarre'); ?></label>
                                <label><input type="checkbox" name="appearance[]" value="Inappropriate"<?php echo checkboxChecked($appearance, "Inappropriate"); ?>> <?php echo xlt('Inappropriate'); ?></label>
                            </fieldset>

                            <!-- Attention -->
                            <fieldset>
                                <legend><?php echo xlt('Attention'); ?></legend>
                                <label><input type="radio" name="attention" value="Normal"<?php echo radioChecked('attention', 'Normal'); ?>> <?php echo xlt('Normal'); ?></label>
                                <label><input type="radio" name="attention" value="Easily Distracted"<?php echo radioChecked('attention', 'Easily Distracted'); ?>> <?php echo xlt('Easily Distracted'); ?></label>
                            </fieldset>

                            <!-- Concentration -->
                            <fieldset>
                                <legend><?php echo xlt('Concentration'); ?></legend>
                                <label><input type="radio" name="concentration" value="Good" <?php echo radioChecked('concentration', 'Good'); ?>> <?php echo xlt('Good'); ?></label>
                                <label><input type="radio" name="concentration" value="Poor" <?php echo radioChecked('concentration', 'Poor'); ?>> <?php echo xlt('Poor'); ?></label>
                            </fieldset>

                            <!-- Hallucinations -->
                            <fieldset>
                                <legend><?php echo xlt('Hallucinations'); ?></legend>
                                <label><input type="checkbox" name="hallucinations[]" value="None" <?php echo checkboxChecked($hallucinations, "None"); ?>> <?php echo xlt('None'); ?></label>
                                <label><input type="checkbox" name="hallucinations[]" value="Auditory" <?php echo checkboxChecked($hallucinations, "Auditory"); ?>> <?php echo xlt('Auditory'); ?></label>
                                <label><input type="checkbox" name="hallucinations[]" value="Visual"<?php echo checkboxChecked($hallucinations, "Visual"); ?>> <?php echo xlt('Visual'); ?></label>
                                <label><input type="checkbox" name="hallucinations[]" value="Olfactory"<?php echo checkboxChecked($hallucinations, "Olfactory"); ?>> <?php echo xlt('Olfactory'); ?></label>
                                <label><input type="checkbox" name="hallucinations[]" value="Command"<?php echo checkboxChecked($hallucinations, "Command"); ?>> <?php echo xlt('Command'); ?></label>
                            </fieldset>

                            <!-- Delusions -->
                            <fieldset>
                                <legend><?php echo xlt('Delusions'); ?></legend>
                                <label><input type="checkbox" name="delusion[]" value="None" <?php echo checkboxChecked($delusion, "None"); ?>> <?php echo xlt('None'); ?></label>
                                <label><input type="checkbox" name="delusion[]" value="Paranoid" <?php echo checkboxChecked($delusion, "Paranoid"); ?>> <?php echo xlt('Paranoid'); ?></label>
                                <label><input type="checkbox" name="delusion[]" value="Grandeur" <?php echo checkboxChecked($delusion, "Grandeur"); ?>> <?php echo xlt('Grandeur'); ?></label>
                                <label><input type="checkbox" name="delusion[]" value="Reference" <?php echo checkboxChecked($delusion, "Reference"); ?>> <?php echo xlt('Reference'); ?></label>
                                <label><input type="checkbox" name="delusion[]" value="Other" <?php echo checkboxChecked($delusion, "Other"); ?>> <?php echo xlt('Other'); ?></label>
                            </fieldset>

                            <!-- Memory -->
                            <fieldset>
                                <legend><?php echo xlt('Memory'); ?></legend>
                                <label><input type="checkbox" name="memory[]" value="Intact" <?php echo checkboxChecked($memory, "Intact"); ?>> <?php echo xlt('Intact'); ?></label>
                                <label><input type="checkbox" name="memory[]" value="Immediate Impaired" <?php echo checkboxChecked($memory, "Immediate Impaired"); ?>> <?php echo xlt('Immediate Impaired'); ?></label>
                                <label><input type="checkbox" name="memory[]" value="Recent Impaired" <?php echo checkboxChecked($memory, "Recent Impaired"); ?>> <?php echo xlt('Recent Impaired'); ?></label>
                                <label><input type="checkbox" name="memory[]" value="Remote Impaired" <?php echo checkboxChecked($memory, "Remote Impaired"); ?>> <?php echo xlt('Remote Impaired'); ?></label>
                            </fieldset>

                            <!-- Intelligence -->
                            <fieldset>
                                <legend><?php echo xlt('Intelligence'); ?></legend>
                                <label><input type="radio" name="intelligence" value="Normal" <?php echo radioChecked('intelligence', 'Normal'); ?>> <?php echo xlt('Normal'); ?></label>
                                <label><input type="radio" name="intelligence" value="Low" <?php echo radioChecked('intelligence', 'Low'); ?>> <?php echo xlt('Low'); ?></label>
                            </fieldset>

                            <!-- Orientation -->
                            <fieldset>
                                <legend><?php echo xlt('Orientation'); ?></legend>
                                <label><input type="checkbox" name="orientation[]" value="Person" <?php echo checkboxChecked($orientation, "Person"); ?>> <?php echo xlt('Person'); ?></label>
                                <label><input type="checkbox" name="orientation[]" value="Place" <?php echo checkboxChecked($orientation, "Place"); ?>> <?php echo xlt('Place'); ?></label>
                                <label><input type="checkbox" name="orientation[]" value="Time" <?php echo checkboxChecked($orientation, "Time"); ?>> <?php echo xlt('Time'); ?></label>
                            </fieldset>

                            <!-- Social Judgement -->
                            <fieldset>
                                <legend><?php echo xlt('Social Judgement'); ?></legend>
                                <label><input type="radio" name="social_judgement" value="Appropriate" <?php echo radioChecked('social_judgement', 'Appropriate'); ?>> <?php echo xlt('Appropriate'); ?></label>
                                <label><input type="radio" name="social_judgement" value="Harmful" <?php echo radioChecked('social_judgement', 'Harmful'); ?>> <?php echo xlt('Harmful'); ?></label>
                                <label><input type="radio" name="social_judgement" value="Unacceptable" <?php echo radioChecked('social_judgement', 'Unacceptable'); ?>> <?php echo xlt('Unacceptable'); ?></label>
                                <label><input type="radio" name="social_judgement" value="Unknown" <?php echo radioChecked('social_judgement', 'Unknown'); ?>> <?php echo xlt('Unknown'); ?></label>
                            </fieldset>

                            <!-- Insight -->
                            <fieldset>
                                <legend><?php echo xlt('Insight'); ?></legend>
                                <label><input type="radio" name="insight" value="Good" <?php echo radioChecked('insight', 'Good'); ?>> <?php echo xlt('Good'); ?></label>
                                <label><input type="radio" name="insight" value="Fair" <?php echo radioChecked('insight', 'Fair'); ?>> <?php echo xlt('Fair'); ?></label>
                                <label><input type="radio" name="insight" value="Poor" <?php echo radioChecked('insight', 'Poor'); ?>> <?php echo xlt('Poor'); ?></label>
                                <label><input type="radio" name="insight" value="Denial" <?php echo radioChecked('insight', 'Denial'); ?>> <?php echo xlt('Denial'); ?></label>
                                <label><input type="radio" name="insight" value="Blames Others" <?php echo radioChecked('insight', 'Blames Others'); ?>> <?php echo xlt('Blames Others'); ?></label>
                            </fieldset>

                            <!-- Thought Content -->
                            <fieldset>
                                <legend><?php echo xlt('Thought Content'); ?></legend>
                                <label><input type="checkbox" name="thought_content[]" value="Appropriate" <?php echo checkboxChecked($thought_content, "Appropriate"); ?>> <?php echo xlt('Appropriate'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Suicide" <?php echo checkboxChecked($thought_content, "Suicide"); ?>> <?php echo xlt('Suicide'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Homicide" <?php echo checkboxChecked($thought_content, "Homicide"); ?>> <?php echo xlt('Homicide'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Illness" <?php echo checkboxChecked($thought_content, "Illness"); ?>> <?php echo xlt('Illness'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Obsession" <?php echo checkboxChecked($thought_content, "Obsession"); ?>> <?php echo xlt('Obsession'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Compulsions" <?php echo checkboxChecked($thought_content, "Compulsions"); ?>> <?php echo xlt('Compulsions'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Fears" <?php echo checkboxChecked($thought_content, "Fears"); ?>> <?php echo xlt('Fears'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Somatic Complaints" <?php echo checkboxChecked($thought_content, "Somatic Complaints"); ?>> <?php echo xlt('Somatic Complaints'); ?></label>
                                <label><input type="checkbox" name="thought_content[]" value="Other" <?php echo checkboxChecked($thought_content, "Other"); ?>> <?php echo xlt('Other'); ?></label>
                            </fieldset>

                            <!-- Affect -->
                            <fieldset>
                                <legend><?php echo xlt('Affect'); ?></legend>
                                <label><input type="radio" name="affect" value="Appropriate" <?php echo radioChecked('affect', 'Appropriate'); ?>> <?php echo xlt('Appropriate'); ?></label>
                                <label><input type="radio" name="affect" value="Inappropriate" <?php echo radioChecked('affect', 'Inappropriate'); ?>> <?php echo xlt('Inappropriate'); ?></label>
                                <input type="text" name="affect_description" placeholder="<?php echo xla('Describe if Inappropriate'); ?>" value="<?php echo attr($obj['affect_description'] ?? ''); ?>">
                            </fieldset>

                            <!-- Mood -->
                            <fieldset>  
                                <legend><?php echo xlt('Mood'); ?></legend>
                                <label><input type="radio" name="mood" value="Euthymic" <?php echo radioChecked('mood', 'Euthymic'); ?>> <?php echo xlt('Euthymic'); ?></label>
                                <label><input type="radio" name="mood" value="Other" <?php echo radioChecked('mood', 'Other'); ?>> <?php echo xlt('Other'); ?></label>
                                <input type="text" name="mood_description" placeholder="<?php echo xla('Describe if Other'); ?>" value="<?php echo attr($obj['mood_description'] ?? ''); ?>">
                            </fieldset>

                            <!-- Speech -->
                            <fieldset>
                                <legend><?php echo xlt('Speech'); ?></legend>
                                <label><input type="checkbox" name="speech[]" value="Normal" <?php echo checkboxChecked($speech, "Normal"); ?>> <?php echo xlt('Normal'); ?></label>
                                <label><input type="checkbox" name="speech[]" value="Slurred" <?php echo checkboxChecked($speech, "Slurred"); ?>> <?php echo xlt('Slurred'); ?></label>
                                <label><input type="checkbox" name="speech[]" value="Slow" <?php echo checkboxChecked($speech, "Slow"); ?>> <?php echo xlt('Slow'); ?></label>
                                <label><input type="checkbox" name="speech[]" value="Pressured" <?php echo checkboxChecked($speech, "Pressured"); ?>> <?php echo xlt('Pressured'); ?></label>
                                <label><input type="checkbox" name="speech[]" value="Loud" <?php echo checkboxChecked($speech, "Loud"); ?>> <?php echo xlt('Loud'); ?></label>
                            </fieldset>

                            <!-- Behavior -->
                            <fieldset>
                                <legend><?php echo xlt('Behavior'); ?></legend>
                                <label><input type="checkbox" name="behavior[]" value="Appropriate" <?php echo checkboxChecked($behavior, "Appropriate"); ?>> <?php echo xlt('Appropriate'); ?></label>
                                <label><input type="checkbox" name="behavior[]" value="Anxious" <?php echo checkboxChecked($behavior, "Anxious"); ?>> <?php echo xlt('Anxious'); ?></label>
                                <label><input type="checkbox" name="behavior[]" value="Agitated" <?php echo checkboxChecked($behavior, "Agitated"); ?>> <?php echo xlt('Agitated'); ?></label>
                                <label><input type="checkbox" name="behavior[]" value="Guarded" <?php echo checkboxChecked($behavior, "Guarded"); ?>> <?php echo xlt('Guarded'); ?></label>
                                <label><input type="checkbox" name="behavior[]" value="Hostile" <?php echo checkboxChecked($behavior, "Hostile"); ?>> <?php echo xlt('Hostile'); ?></label>
                                <label><input type="checkbox" name="behavior[]" value="Uncooperative" <?php echo checkboxChecked($behavior, "Uncooperative"); ?>> <?php echo xlt('Uncooperative'); ?></label>
                            </fieldset>

                            <!-- Thought Disorder -->
                            <fieldset>
                                <legend><?php echo xlt('Thought Disorder'); ?></legend>
                                <label><input type="checkbox" name="thought_disorder[]" value="Normal" <?php echo checkboxChecked($thought_disorder, "Normal"); ?>> <?php echo xlt('Normal'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Narcissistic" <?php echo checkboxChecked($thought_disorder, "Narcissistic"); ?>> <?php echo xlt('Narcissistic'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Homicide" <?php echo checkboxChecked($thought_disorder, "Homicide"); ?>> <?php echo xlt('Homicide'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Ideas of Reference" <?php echo checkboxChecked($thought_disorder, "Ideas of Reference"); ?>> <?php echo xlt('Ideas of Reference'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Tangential" <?php echo checkboxChecked($thought_disorder, "Tangential"); ?>> <?php echo xlt('Tangential'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Loose Associations" <?php echo checkboxChecked($thought_disorder, "Loose Associations"); ?>> <?php echo xlt('Loose Associations'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Confusion" <?php echo checkboxChecked($thought_disorder, "Confusion"); ?>> <?php echo xlt('Confusion'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Thought Blocking" <?php echo checkboxChecked($thought_disorder, "Thought Blocking"); ?>> <?php echo xlt('Thought Blocking'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Obsession" <?php echo checkboxChecked($thought_disorder, "Obsession"); ?>> <?php echo xlt('Obsession'); ?></label>
                                <label><input type="checkbox" name="thought_disorder[]" value="Flight of Ideas" <?php echo checkboxChecked($thought_disorder, "Flight of Ideas"); ?>> <?php echo xlt('Flight of Ideas'); ?></label>
                            </fieldset>

                            <!-- Sleep -->
                            <fieldset>
                                <legend><?php echo xlt('Sleep'); ?></legend>
                                <label><input type="radio" name="sleep" value="No Change" <?php echo radioChecked('sleep', 'No Change'); ?>> <?php echo xlt('No Change'); ?></label>
                                <label><input type="radio" name="sleep" value="Interrupted" <?php echo radioChecked('sleep', 'Interrupted'); ?>> <?php echo xlt('Interrupted'); ?></label>
                                <label><input type="radio" name="sleep" value="Increased" <?php echo radioChecked('sleep', 'Increased'); ?>> <?php echo xlt('Increased'); ?></label>
                                <label><input type="radio" name="sleep" value="Decreased" <?php echo radioChecked('sleep', 'Decreased'); ?>> <?php echo xlt('Decreased'); ?></label>
                            </fieldset>

                            <!-- Appetite -->
                            <fieldset>
                                <legend><?php echo xlt('Appetite'); ?></legend>
                                <label><input type="radio" name="appetite" value="Increased" <?php echo radioChecked('appetite', 'Increased'); ?>> <?php echo xlt('Increased'); ?></label>
                                <label><input type="radio" name="appetite" value="Decreased" <?php echo radioChecked('appetite', 'Decreased'); ?>> <?php echo xlt('Decreased'); ?></label>
                                <label><input type="radio" name="appetite" value="No Change" <?php echo radioChecked('appetite', 'No Change'); ?>> <?php echo xlt('No Change'); ?></label>
                            </fieldset>

                            <!-- Weight -->
                            <fieldset>
                                <legend><?php echo xlt('Weight'); ?></legend>
                                <label><input type="checkbox" name="weight[]" value="Weight Loss" <?php echo checkboxChecked($weight, "Weight Loss"); ?>> <?php echo xlt('Weight Loss'); ?></label>
                                <label><input type="checkbox" name="weight[]" value="Weight Gain" <?php echo checkboxChecked($weight, "Weight Gain"); ?>> <?php echo xlt('Weight Gain'); ?></label>
                            </fieldset>

                            <!-- Eating Disorders -->
                            <fieldset>
                                <legend><?php echo xlt('Eating Disorders'); ?></legend>
                                <label><input type="checkbox" name="eating_disorders[]" value="Anorexia" <?php echo checkboxChecked($eating_disorders, "Anorexia"); ?>> <?php echo xlt('Anorexia'); ?></label>
                                <label><input type="checkbox" name="eating_disorders[]" value="Bulemia" <?php echo checkboxChecked($eating_disorders, "Bulemia"); ?>> <?php echo xlt('Bulemia'); ?></label>
                            </fieldset>

                            <!-- Self-Harm -->
                            <fieldset>
                                <legend><?php echo xlt('Self-Mutilation / Cutting Behaviors'); ?></legend>
                                <label><input type="checkbox" name="self_harm[]" value="Self-Mutilation" <?php echo checkboxChecked($self_harm, "Self-Mutilation"); ?>> <?php echo xlt('Self-Mutilation'); ?></label>
                                <label><input type="checkbox" name="self_harm[]" value="Cutting" <?php echo checkboxChecked($self_harm, "Cutting"); ?>> <?php echo xlt('Cutting'); ?></label>
                            </fieldset>

                            <hr />
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="btn-group" role="group">
                                    <button type="submit" onclick="top.restoreSession()" class="btn btn-primary btn-save"><?php echo xlt('Save'); ?></button>
                                    <button type="button" class="btn btn-secondary btn-cancel" onclick="top.restoreSession(); parent.closeTab(window.name, false);"><?php echo xlt('Cancel'); ?></button>
                                </div>
                                <input type="hidden" id="clickId" value="" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>