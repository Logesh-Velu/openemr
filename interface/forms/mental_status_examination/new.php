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
use OpenEMR\Services\ClinicalNotesService;

$returnurl = 'encounter_top.php';
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
                <h2><?php echo xlt('Mental Status Examination'); ?></h2>
                <form method='post' name='my_form' action='<?php echo $rootdir ?>/forms/mental_status_examination/save.php?mode=new'>
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                 
                    <div class="container-fluid">
                     
                        <div class="tb_row" id="tb_row">
                          <fieldset>
                            <legend>Appearance</legend>
                            <label><input type="checkbox" name="appearance[]" value="Well-groomed"> Well-groomed</label>
                            <label><input type="checkbox" name="appearance[]" value="Disheveled"> Disheveled</label>
                            <label><input type="checkbox" name="appearance[]" value="Bizarre"> Bizarre</label>
                            <label><input type="checkbox" name="appearance[]" value="Inappropriate"> Inappropriate</label>
                        </fieldset>

                        <!-- Attention -->
                        <fieldset>
                            <legend>Attention</legend>
                            <label><input type="radio" name="attention" value="Normal"> Normal</label>
                            <label><input type="radio" name="attention" value="Easily Distracted"> Easily Distracted</label>
                        </fieldset>

                        <!-- Concentration -->
                        <fieldset>
                            <legend>Concentration</legend>
                            <label><input type="radio" name="concentration" value="Good"> Good</label>
                            <label><input type="radio" name="concentration" value="Poor"> Poor</label>
                        </fieldset>

                        <!-- Hallucinations -->
                        <fieldset>
                            <legend>Hallucinations</legend>
                            <label><input type="checkbox" name="hallucinations[]" value="None"> None</label>
                            <label><input type="checkbox" name="hallucinations[]" value="Auditory"> Auditory</label>
                            <label><input type="checkbox" name="hallucinations[]" value="Visual"> Visual</label>
                            <label><input type="checkbox" name="hallucinations[]" value="Olfactory"> Olfactory</label>
                            <label><input type="checkbox" name="hallucinations[]" value="Command"> Command</label>
                        </fieldset>

                        <!-- Delusions -->
                        <fieldset>
                            <legend>Delusions</legend>
                            <label><input type="checkbox" name="delusion[]" value="None"> None</label>
                            <label><input type="checkbox" name="delusion[]" value="Paranoid"> Paranoid</label>
                            <label><input type="checkbox" name="delusion[]" value="Grandeur"> Grandeur</label>
                            <label><input type="checkbox" name="delusion[]" value="Reference"> Reference</label>
                            <label><input type="checkbox" name="delusion[]" value="Other"> Other</label>
                        </fieldset>

                        <!-- Memory -->
                        <fieldset>
                            <legend>Memory</legend>
                            <label><input type="checkbox" name="memory[]" value="Intact"> Intact</label>
                            <label><input type="checkbox" name="memory[]" value="Immediate Impaired"> Immediate Impaired</label>
                            <label><input type="checkbox" name="memory[]" value="Recent Impaired"> Recent Impaired</label>
                            <label><input type="checkbox" name="memory[]" value="Remote Impaired"> Remote Impaired</label>
                        </fieldset>

                        <!-- Intelligence -->
                        <fieldset>
                            <legend>Intelligence</legend>
                            <label><input type="radio" name="intelligence" value="Normal"> Normal</label>
                            <label><input type="radio" name="intelligence" value="Low"> Low</label>
                        </fieldset>

                        <!-- Orientation -->
                        <fieldset>
                            <legend>Orientation</legend>
                            <label><input type="checkbox" name="orientation[]" value="Person"> Person</label>
                            <label><input type="checkbox" name="orientation[]" value="Place"> Place</label>
                            <label><input type="checkbox" name="orientation[]" value="Time"> Time</label>
                        </fieldset>

                        <!-- Social Judgement -->
                        <fieldset>
                            <legend>Social Judgement</legend>
                            <label><input type="radio" name="social_judgement" value="Appropriate"> Appropriate</label>
                            <label><input type="radio" name="social_judgement" value="Harmful"> Harmful</label>
                            <label><input type="radio" name="social_judgement" value="Unacceptable"> Unacceptable</label>
                            <label><input type="radio" name="social_judgement" value="Unknown"> Unknown</label>
                        </fieldset>

                        <!-- Insight -->
                        <fieldset>
                            <legend>Insight</legend>
                            <label><input type="radio" name="insight" value="Good"> Good</label>
                            <label><input type="radio" name="insight" value="Fair"> Fair</label>
                            <label><input type="radio" name="insight" value="Poor"> Poor</label>
                            <label><input type="radio" name="insight" value="Denial"> Denial</label>
                            <label><input type="radio" name="insight" value="Blames Others"> Blames Others</label>
                        </fieldset>

                        <!-- Thought Content -->
                        <fieldset>
                            <legend>Thought Content</legend>
                            <label><input type="checkbox" name="thought_content[]" value="Appropriate"> Appropriate</label>
                            <label><input type="checkbox" name="thought_content[]" value="Suicide"> Suicide</label>
                            <label><input type="checkbox" name="thought_content[]" value="Homicide"> Homicide</label>
                            <label><input type="checkbox" name="thought_content[]" value="Illness"> Illness</label>
                            <label><input type="checkbox" name="thought_content[]" value="Obsession"> Obsession</label>
                            <label><input type="checkbox" name="thought_content[]" value="Compulsions"> Compulsions</label>
                            <label><input type="checkbox" name="thought_content[]" value="Fears"> Fears</label>
                            <label><input type="checkbox" name="thought_content[]" value="Somatic Complaints"> Somatic Complaints</label>
                            <label><input type="checkbox" name="thought_content[]" value="Other"> Other</label>
                        </fieldset>

                        <!-- Affect -->
                        <fieldset>
                            <legend>Affect</legend>
                            <label><input type="radio" name="affect" value="Appropriate"> Appropriate</label>
                            <label><input type="radio" name="affect" value="Inappropriate"> Inappropriate</label>
                            <input type="text" name="affect_description" placeholder="Describe if Inappropriate">
                        </fieldset>

                        <!-- Mood -->
                        <fieldset>
                            <legend>Mood</legend>
                            <label><input type="radio" name="mood" value="Euthymic"> Euthymic</label>
                            <label><input type="radio" name="mood" value="Other"> Other</label>
                            <input type="text" name="mood_description" placeholder="Describe if Other">
                        </fieldset>

                        <!-- Speech -->
                        <fieldset>
                            <legend>Speech</legend>
                            <label><input type="checkbox" name="speech[]" value="Normal"> Normal</label>
                            <label><input type="checkbox" name="speech[]" value="Slurred"> Slurred</label>
                            <label><input type="checkbox" name="speech[]" value="Slow"> Slow</label>
                            <label><input type="checkbox" name="speech[]" value="Pressured"> Pressured</label>
                            <label><input type="checkbox" name="speech[]" value="Loud"> Loud</label>
                        </fieldset>

                        <!-- Behavior -->
                        <fieldset>
                            <legend>Behavior</legend>
                            <label><input type="checkbox" name="behavior[]" value="Appropriate"> Appropriate</label>
                            <label><input type="checkbox" name="behavior[]" value="Anxious"> Anxious</label>
                            <label><input type="checkbox" name="behavior[]" value="Agitated"> Agitated</label>
                            <label><input type="checkbox" name="behavior[]" value="Guarded"> Guarded</label>
                            <label><input type="checkbox" name="behavior[]" value="Hostile"> Hostile</label>
                            <label><input type="checkbox" name="behavior[]" value="Uncooperative"> Uncooperative</label>
                        </fieldset>

                        <!-- Thought Disorder -->
                        <fieldset>
                            <legend>Thought Disorder</legend>
                            <label><input type="checkbox" name="thought_disorder[]" value="Normal"> Normal</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Narcissistic"> Narcissistic</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Homicide"> Homicide</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Ideas of Reference"> Ideas of Reference</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Tangential"> Tangential</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Loose Associations"> Loose Associations</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Confusion"> Confusion</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Thought Blocking"> Thought Blocking</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Obsession"> Obsession</label>
                            <label><input type="checkbox" name="thought_disorder[]" value="Flight of Ideas"> Flight of Ideas</label>
                        </fieldset>

                        <!-- Sleep -->
                        <fieldset>
                            <legend>Sleep</legend>
                            <label><input type="radio" name="sleep" value="No Change"> No Change</label>
                            <label><input type="radio" name="sleep" value="Interrupted"> Interrupted</label>
                            <label><input type="radio" name="sleep" value="Increased"> Increased</label>
                            <label><input type="radio" name="sleep" value="Decreased"> Decreased</label>
                        </fieldset>

                        <!-- Appetite -->
                        <fieldset>
                            <legend>Appetite</legend>
                            <label><input type="radio" name="appetite" value="Increased"> Increased</label>
                            <label><input type="radio" name="appetite" value="Decreased"> Decreased</label>
                            <label><input type="radio" name="appetite" value="No Change"> No Change</label>
                        </fieldset>

                        <!-- Weight -->
                        <fieldset>
                            <legend>Weight</legend>
                            <label><input type="checkbox" name="weight[]" value="Weight Loss"> Weight Loss</label>
                            <label><input type="checkbox" name="weight[]" value="Weight Gain"> Weight Gain</label>
                        </fieldset>

                        <!-- Eating Disorders -->
                        <fieldset>
                            <legend>Eating Disorders</legend>
                            <label><input type="checkbox" name="eating_disorders[]" value="Anorexia"> Anorexia</label>
                            <label><input type="checkbox" name="eating_disorders[]" value="Bulemia"> Bulemia</label>
                        </fieldset>

                        <!-- Self-Harm -->
                        <fieldset>
                            <legend>Self-Mutilation / Cutting Behaviors</legend>
                            <label><input type="checkbox" name="self_harm[]" value="Self-Mutilation"> Self-Mutilation</label>
                            <label><input type="checkbox" name="self_harm[]" value="Cutting"> Cutting</label>
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
