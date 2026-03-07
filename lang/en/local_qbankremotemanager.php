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

// Author of this plugin: Peter Vanát <vanat.peter@gmail.com>

/**
 * English localization for QBank Remote Manager plugin.
 * @package local_qbankremotemanager
 * @copyright 2026 Peter Vanát <vanat.peter@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'QBank Remote Manager';
$string['service_name'] = 'QBank Remote Manager API';

$string['itemid_desc'] = 'The ID of the file uploaded in the draft area.';
$string['config_desc'] = 'Structure containing quiz configuration settings.';

$string['quizname_desc'] = 'The name of the quiz.';
$string['section_desc'] = 'The section number within the course where the quiz will be placed.';
$string['courseid_desc'] = 'The ID of the course.';
$string['gradepass_desc'] = 'Grade to pass the quiz.';
$string['showuserpicture_desc'] = 'How to show user picture in quiz.';
$string['timelimit_desc'] = 'Time limit for the quiz in seconds.';
$string['intro_desc'] = 'The introductory text for the quiz in HTML format.';
$string['showdescription_desc'] = 'Whether to show the introduction on the course page (1 for yes, 0 for no).';
$string['timeopen_desc'] = 'Unix timestamp when the quiz opens.';
$string['timeclose_desc'] = 'Unix timestamp when the quiz closes.';
$string['overduehandling_desc'] = 'The method used to handle overdue attempts (e.g., autosubmit).';
$string['graceperiod_desc'] = 'The submission grace period in seconds.';
$string['gradecat_desc'] = 'The name of the grade category. This is Case Sensitive.';
$string['grade_desc'] = 'The maximum grade for the quiz.';
$string['attempts_desc'] = 'The number of allowed attempts (0 for unlimited).';
$string['grademethod_desc'] = 'The grading method for multiple attempts.';
$string['questionsperpage_desc'] = 'Number of questions per page (0 for all on one page).';
$string['navmethod_desc'] = 'The navigation method: "free" or "sequential".';
$string['shuffleanswers_desc'] = 'Whether to shuffle answers within questions (1 for yes, 0 for no).';
$string['preferredbehaviour_desc'] = 'The question behavior (e.g., "deferredfeedback").';
$string['canredoquestions_desc'] = 'Allow redo within an attempt (1 for yes, 0 for no).';
$string['attemptonlast_desc'] = 'Each attempt builds on the last (1 for yes, 0 for no).';

$string['attemptduring_desc'] = 'Review option: Show attempt during the quiz. If set to -1, system default is used.';
$string['correctnessduring_desc'] = 'Review option: Show correctness during the quiz. If set to -1, system default is used.';
$string['maxmarksduring_desc'] = 'Review option: Show maxmarks during the quiz. If set to -1, system default is used.';
$string['marksduring_desc'] = 'Review option: Show marks during the quiz. If set to -1, system default is used.';
$string['specificfeedbackduring_desc'] = 'Review option: Show specificfeedback during the quiz. If set to -1, system default is used.';
$string['generalfeedbackduring_desc'] = 'Review option: Show generalfeedback during the quiz. If set to -1, system default is used.';
$string['rightanswerduring_desc'] = 'Review option: Show rightanswer during the quiz. If set to -1, system default is used.';
$string['overallfeedbackduring_desc'] = 'Review option: Show overallfeedback during the quiz. If set to -1, system default is used.';

$string['attemptimmediately_desc'] = 'Review option: Show attempt immediately after. If set to -1, system default is used.';
$string['correctnessimmediately_desc'] = 'Review option: Show correctness immediately after. If set to -1, system default is used.';
$string['maxmarksimmediately_desc'] = 'Review option: Show maxmarks immediately after. If set to -1, system default is used.';
$string['marksimmediately_desc'] = 'Review option: Show marks immediately after. If set to -1, system default is used.';
$string['specificfeedbackimmediately_desc'] = 'Review option: Show specificfeedback immediately after. If set to -1, system default is used.';
$string['generalfeedbackimmediately_desc'] = 'Review option: Show generalfeedback immediately after. If set to -1, system default is used.';
$string['rightanswerimmediately_desc'] = 'Review option: Show rightanswer immediately after. If set to -1, system default is used.';
$string['overallfeedbackimmediately_desc'] = 'Review option: Show overallfeedback immediately after. If set to -1, system default is used.';

$string['attemptopen_desc'] = 'Review option: Show attempt while quiz is open. If set to -1, system default is used.';
$string['correctnessopen_desc'] = 'Review option: Show correctness while quiz is open. If set to -1, system default is used.';
$string['maxmarksopen_desc'] = 'Review option: Show maxmarks while quiz is open. If set to -1, system default is used.';
$string['marksopen_desc'] = 'Review option: Show marks while quiz is open. If set to -1, system default is used.';
$string['specificfeedbackopen_desc'] = 'Review option: Show specificfeedback while quiz is open. If set to -1, system default is used.';
$string['generalfeedbackopen_desc'] = 'Review option: Show generalfeedback while quiz is open. If set to -1, system default is used.';
$string['rightansweropen_desc'] = 'Review option: Show rightanswer while quiz is open. If set to -1, system default is used.';
$string['overallfeedbackopen_desc'] = 'Review option: Show overallfeedback while quiz is open. If set to -1, system default is used.';

$string['attemptclosed_desc'] = 'Review option: Show attempt after quiz is closed. If set to -1, system default is used.';
$string['correctnessclosed_desc'] = 'Review option: Show correctness after quiz is closed. If set to -1, system default is used.';
$string['maxmarksclosed_desc'] = 'Review option: Show maxmarks after quiz is closed. If set to -1, system default is used.';
$string['marksclosed_desc'] = 'Review option: Show marks after quiz is closed. If set to -1, system default is used.';
$string['specificfeedbackclosed_desc'] = 'Review option: Show specificfeedback after quiz is closed. If set to -1, system default is used.';
$string['generalfeedbackclosed_desc'] = 'Review option: Show generalfeedback after quiz is closed. If set to -1, system default is used.';
$string['rightanswerclosed_desc'] = 'Review option: Show rightanswer after quiz is closed. If set to -1, system default is used.';
$string['overallfeedbackclosed_desc'] = 'Review option: Show overallfeedback after quiz is closed. If set to -1, system default is used.';

$string['showuserpicture_desc'] = 'Whether to show the user\'s picture during attempts (1/0).';
$string['decimalpoints_desc'] = 'Decimal places in grades for the quiz.';
$string['questiondecimalpoints_desc'] = 'Decimal places in grades for individual questions.';
$string['quizpassword_desc'] = 'Password required to access the quiz.';
$string['browsersecurity_desc'] = 'Browser security setting (e.g., "none" or "safebrowser").';
$string['allowofflineattempts_desc'] = 'Whether to allow offline attempts in the Mobile app (1/0).';

$string['visible_desc'] = 'Visibility of the quiz (1 for show, 0 for hide).';
$string['visibleoncoursepage_desc'] = 'Whether the activity is visible on the course page.';
$string['groupmode_desc'] = 'The group mode of the quiz module.';
$string['groupingid_desc'] = 'The ID of the grouping for this module.';
$string['completion_desc'] = 'Completion tracking setting.';
$string['completionminattempts_desc'] = 'Completion requirement: minimum attempts.';
$string['completionpassgrade_desc'] = 'Completion requirement: passing grade.';
$string['completionattemptsexhausted_desc'] = 'Completion requirement: attempts exhausted.';
$string['completionexpected_desc'] = 'The date when the activity is expected to be completed.';

$string['sr_desc'] = 'Section return: Reference to the course section for redirection (internal Moodle parameter).';
$string['beforemod_desc'] = 'The ID of the module before which this quiz should be placed (used for ordering).';
$string['competency_rule_desc'] = 'The rule for handling competencies when the quiz is completed (0 = do nothing, 1 = complete competency, etc.).';
$string['override_grade_desc'] = 'An optional grade value that overrides the calculated grade for this activity.';

$string['coursemodule_desc'] = 'The course module ID.';
$string['module_desc'] = 'The module type ID.';
$string['instance_desc'] = 'The instance ID.';

$string['amihererestext_desc'] = 'This will always contain "Yes I am", and it represents that this plugin is available.';

$string['quizid_desc'] = 'Id of the newly created quiz.';
$string['status_desc'] = 'Status of the operation. Can be "OK" or "ERROR".';
$string['num_of_questions_desc'] = 'Number of upload questions.';
$string['error_message_desc'] = 'Error message.';

$string['coursecontextid_desc'] = 'ID of the course context.';
$string['categories_desc'] = 'List of available categories for export.';
$string['id_desc'] = 'ID of the category.';
$string['title_desc'] = 'Title of the given category.';

$string['badargument_with_expected_values'] = 'Wrong value for {$a->key} entered. Expected one of: {$a->expected} but was {$a->actual}.';
$string['badargument_with_range'] = 'Wrong value for {$a->key} entered. Expected value in range where min = {$a->min} and max = {$a->max} but was {$a->actual}.';
$string['badargument_bool'] = 'Wrong value for {$a->key} entered. Expected 1 or 0 but was {$a->actual}.';
$string['gradepass_too_low'] = 'Grade pass value cannot be lower than 0';
$string['gradepass_too_high'] = 'Grade pass value cannot exceed grade value.';
$string['browser_security_not_available'] = 'It was not possible to retrieve valid values for browsersecurity parameter.';
$string['internal_error'] = 'Something went wrong inside of the plugin logic.';
