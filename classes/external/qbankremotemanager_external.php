<?php
/**
 * Implementation of the REST API functions for QBank Remote Manager plugin.
 *
 * @package    local_qbankremotemanager
 * @copyright  2026 Peter Vanát <vanat.peter@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
namespace local_qbankremotemanager\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use context_course;
use context_user;
use stdClass;
use context;
use qformat_xml;
use moodle_exception;
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\helper as qbank_helper;
use qbank_managecategories\helper as manage_categories_helper;
use mod_quiz\quiz_settings;
use mod_quiz\access_manager;
use \question_engine;
use core_courseformat\base as course_format;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/questionlib.php");
require_once("$CFG->dirroot/question/format/xml/format.php"); 
require_once("$CFG->dirroot/course/modlib.php");

// needed for Moodle version 4.0 (function get_module_from_cmid is there)
require_once("$CFG->dirroot/question/editlib.php");

//needed for defined constants like QUIZ_MAX_DECIMAL_OPTION
require_once("$CFG->dirroot/mod/quiz/lib.php");

//needed for functions: quiz_get_overdue_handling_options, quiz_get_user_image_options, quiz_questions_per_page_options, quiz_get_grading_options
require_once("$CFG->dirroot/mod/quiz/locallib.php");

//needed for function: grade_get_categories_menu
require_once("$CFG->libdir/gradelib.php");


class qbankremotemanager_external extends external_api {

    public static function am_i_here_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Used to check if this plugin is available with, without making moodle to throw exception.
     */
    public static function am_i_here() {
        return ['res_text' => "Yes I am"];
    }

    public static function am_i_here_returns() {
        return new external_single_structure([
            'res_text' => new external_value(PARAM_TEXT, get_string('amihererestext_desc', 'local_qbankremotemanager')),
        ]);
    }

    public static function upload_quiz_parameters() {
        return new external_function_parameters([
            'config' => new external_single_structure([
                'quizname'                    => new external_value(PARAM_TEXT, get_string('quizname_desc', 'local_qbankremotemanager')),
                'courseid'                    => new external_value(PARAM_INT, get_string('courseid_desc', 'local_qbankremotemanager')),
                'section'                     => new external_value(PARAM_INT, get_string('section_desc', 'local_qbankremotemanager')), 
                'gradepass'                   => new external_value(PARAM_INT, get_string('gradepass_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'showuserpicture'             => new external_value(PARAM_TEXT, get_string('showuserpicture_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'attemptonlast'               => new external_value(PARAM_INT, get_string('attemptonlast_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'canredoquestions'            => new external_value(PARAM_INT, get_string('canredoquestions_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'preferredbehaviour'          => new external_value(PARAM_TEXT, get_string('preferredbehaviour_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'shuffleanswers'              => new external_value(PARAM_INT, get_string('shuffleanswers_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'navmethod'                   => new external_value(PARAM_TEXT, get_string('navmethod_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'questionsperpage'            => new external_value(PARAM_INT, get_string('questionsperpage_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'grademethod'                 => new external_value(PARAM_TEXT, get_string('grademethod_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'attempts'                    => new external_value(PARAM_INT, get_string('attempts_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'gradecat'                    => new external_value(PARAM_TEXT, get_string('gradecat_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'graceperiod'                 => new external_value(PARAM_INT, get_string('graceperiod_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'overduehandling'             => new external_value(PARAM_TEXT, get_string('overduehandling_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'timelimit'                   => new external_value(PARAM_INT, get_string('timelimit_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0), 
                'visible'                     => new external_value(PARAM_INT, get_string('visible_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 1), 
                'browsersecurity'             => new external_value(PARAM_TEXT, get_string('browsersecurity_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, '-'), 
                'quizpassword'                => new external_value(PARAM_TEXT, get_string('quizpassword_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, ''), 
                'questiondecimalpoints'       => new external_value(PARAM_INT, get_string('questiondecimalpoints_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'decimalpoints'               => new external_value(PARAM_INT, get_string('decimalpoints_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL), 
                'timeopen'                    => new external_value(PARAM_INT, get_string('timeopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0), 
                'timeclose'                   => new external_value(PARAM_INT, get_string('timeclose_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0), 
                'intro'                       => new external_value(PARAM_RAW, get_string('intro_desc', 'local_qbankremotemanager', VALUE_DEFAULT, '')), 
                'showdescription'             => new external_value(PARAM_INT, get_string('showdescription_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0), 
                'attemptduring'               => new external_value(PARAM_INT, get_string('attemptduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'correctnessduring'           => new external_value(PARAM_INT, get_string('correctnessduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'maxmarksduring'              => new external_value(PARAM_INT, get_string('maxmarksduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'marksduring'                 => new external_value(PARAM_INT, get_string('marksduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'specificfeedbackduring'      => new external_value(PARAM_INT, get_string('specificfeedbackduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'generalfeedbackduring'       => new external_value(PARAM_INT, get_string('generalfeedbackduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'rightanswerduring'           => new external_value(PARAM_INT, get_string('rightanswerduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'overallfeedbackduring'       => new external_value(PARAM_INT, get_string('overallfeedbackduring_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'attemptimmediately'          => new external_value(PARAM_INT, get_string('attemptimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'correctnessimmediately'      => new external_value(PARAM_INT, get_string('correctnessimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'maxmarksimmediately'         => new external_value(PARAM_INT, get_string('maxmarksimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'marksimmediately'            => new external_value(PARAM_INT, get_string('marksimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'specificfeedbackimmediately' => new external_value(PARAM_INT, get_string('specificfeedbackimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'generalfeedbackimmediately'  => new external_value(PARAM_INT, get_string('generalfeedbackimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'rightanswerimmediately'      => new external_value(PARAM_INT, get_string('rightanswerimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'overallfeedbackimmediately'  => new external_value(PARAM_INT, get_string('overallfeedbackimmediately_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'attemptopen'                 => new external_value(PARAM_INT, get_string('attemptopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'correctnessopen'             => new external_value(PARAM_INT, get_string('correctnessopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'maxmarksopen'                => new external_value(PARAM_INT, get_string('maxmarksopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'marksopen'                   => new external_value(PARAM_INT, get_string('marksopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'specificfeedbackopen'        => new external_value(PARAM_INT, get_string('specificfeedbackopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'generalfeedbackopen'         => new external_value(PARAM_INT, get_string('generalfeedbackopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'rightansweropen'             => new external_value(PARAM_INT, get_string('rightansweropen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'overallfeedbackopen'         => new external_value(PARAM_INT, get_string('overallfeedbackopen_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'attemptclosed'               => new external_value(PARAM_INT, get_string('attemptclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'correctnessclosed'           => new external_value(PARAM_INT, get_string('correctnessclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'maxmarksclosed'              => new external_value(PARAM_INT, get_string('maxmarksclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'marksclosed'                 => new external_value(PARAM_INT, get_string('marksclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'specificfeedbackclosed'      => new external_value(PARAM_INT, get_string('specificfeedbackclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'generalfeedbackclosed'       => new external_value(PARAM_INT, get_string('generalfeedbackclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'rightanswerclosed'           => new external_value(PARAM_INT, get_string('rightanswerclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0),
                'overallfeedbackclosed'       => new external_value(PARAM_INT, get_string('overallfeedbackclosed_desc', 'local_qbankremotemanager'), VALUE_DEFAULT, 0)
            ], get_string('config_desc', 'local_qbankremotemanager')),
            "itemid" => new external_value(PARAM_INT, get_string('itemid_desc', 'local_qbankremotemanager'))
        ]);
    }

    /**
     * Definition of function to upload new quiz to selected course.
     * File with questions for the quiz MUST be uploaded beforehand to draft area and retrieved item id must be passed to this function.
     * You can use webservice/upload.php "Endpoint" to upload the file
     * File MUST be in a Moodle XML format
     * 
     * @param object $config      contains configuration for given quiz (quiz name, password, duration etc.)
     * @param int    $itemid      item id for the file with questions in draft area
     * 
     * @return object where quiz ID is returned with the status. The status can be either "OK" or "ERROR", when error is present you will recieve error message with it. If everything went fine you will recieve number of imported questions.
    */
    public static function upload_quiz($config, $itemid) {
        global $DB;

        $params = self::validate_parameters(self::upload_quiz_parameters(), ['config' => $config, 'itemid' => $itemid]);
        $config = $params['config'];

        $course = $DB->get_record('course', ['id' => $config['courseid']], '*', MUST_EXIST);

        $thiscontext = context_course::instance($config['courseid']);
        self::validate_context($thiscontext);

        //verify that user has capabilities to work with question bank and quiz modules
        require_capability('moodle/question:add', $thiscontext);
        require_capability('moodle/question:editall', $thiscontext);
        require_capability('moodle/question:managecategory', $thiscontext);
        require_capability('moodle/question:moveall', $thiscontext);
        require_capability('moodle/question:useall', $thiscontext);

        require_capability('mod/quiz:addinstance', $thiscontext);
        require_capability('mod/quiz:manage', $thiscontext);

        //validate config before importing questions to question bank
        $validated_config = self::prepare_quiz_data($config, $course);
        
        [$defaultcategory, $contexts] = self::get_default_category_and_contexts($thiscontext);

        $added_question_ids = self::import_questions_to_qbank($itemid, $defaultcategory, $config["courseid"], $contexts);

        if(count($added_question_ids) == 0){
            return ["status" => "ERROR", "error_message" => "No questions in file"];
        }

        $validated_config->grade = self::get_sum_of_default_question_grades($added_question_ids);
        
        $cmid = self::add_test($validated_config, $course);

        self::add_questions_to_quiz($cmid, $added_question_ids, $course);

        return ['quizid' => $cmid, 'status' => 'OK', "num_of_questions" => count($added_question_ids)];
    }

    public static function upload_quiz_returns() {
        return new external_single_structure([
            'quizid'           => new external_value(PARAM_INT, get_string('quizid_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL),
            'status'           => new external_value(PARAM_TEXT, get_string('status_desc', 'local_qbankremotemanager')),
            'num_of_questions' => new external_value(PARAM_INT, get_string('num_of_questions_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL),
            'error_message'    => new external_value(PARAM_TEXT, get_string('error_message_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL),
        ]);
    }

    public static function get_question_bank_categories_parameters(){
        return new external_function_parameters([
            "courseid" => new external_value(PARAM_INT, get_string('courseid_desc', 'local_qbankremotemanager'))
        ]);
    }

    /**
     * Function used to categories from question bank with its context. This can be used to export selected category.
     * 
     * @param int $courseid ID of the course we want the question categories of
     * 
     * @return object where you can retrieve the courseContextId and the array of categories, where each category has an ID and the title 
    */
    public static function get_question_bank_categories($courseid){
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        $courseContext = context_course::instance($courseid);
        self::validate_context($courseContext);

        require_capability('moodle/question:editall', $courseContext);
        require_capability('moodle/question:managecategory', $courseContext);

        $allCourseContexts = new question_edit_contexts($courseContext);

        $catmenu = manage_categories_helper::question_category_options($allCourseContexts->all(), false, 0, true, -1, false);
      
        $values = [];
        foreach ($catmenu as $menu) {
            foreach ($menu as $heading => $catlist) {
                foreach ($catlist as $key => $value) {
                    $sanitizedTitle = str_replace("&nbsp;", "", $value); 
                    $values[] = (object) [
                        // not using str_contains to be compatible with PHP 7
                        'id' => strpos($key, ',') !== false ? substr($key, 0, strpos($key, ',')) : $key,
                        'title' => $sanitizedTitle,
                    ];
                }
            }
        }

        return ['courseContextId' => $courseContext->id, "categories" => $values];
    }

    public static function get_question_bank_categories_returns(){
        return new external_single_structure([
            'courseContextId' => new external_value(PARAM_INT, get_string('coursecontextid_desc', 'local_qbankremotemanager')),
            'categories' => new external_multiple_structure(
                new external_single_structure([
                    'id'    => new external_value(PARAM_INT, get_string('id_desc', 'local_qbankremotemanager')),
                    'title' => new external_value(PARAM_TEXT, get_string('title_desc', 'local_qbankremotemanager'))
                ]), get_string('categories_desc', 'local_qbankremotemanager')
            )
        ]);
    }

    public static function upload_questions_parameters(){
        return new external_function_parameters([
            "courseid" => new external_value(PARAM_INT,  get_string('courseid_desc', 'local_qbankremotemanager')),
            'itemid'   => new external_value(PARAM_INT, get_string('itemid_desc', 'local_qbankremotemanager'))
        ]);
    }

    /**
     * Function used to import new questions to question bank in given course.
     * File with questions MUST be imported beforehand to the draft area and you must provide the retrieved item id.
     * You can use webservice/upload.php "Endpoint" to upload the file
     * The only supported file format is Moodle XML.
     * 
     * @param int $courseid ID of the course you want to import the questions to
     * @param int $itemid ID of the file retrieved after uploading the file to the draft area
     * 
     * @return object with the status. The status can be either "OK" or "ERROR", when error is present you will recieve error message with it. If everything went fine you will recieve number of imported questions.
    */
    public static function upload_questions($courseid, $itemid){
        global $DB;

        $params = self::validate_parameters(self::upload_questions_parameters(), ['courseid' => $courseid, 'itemid' => $itemid]);
        
        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);

        $courseContext = \context_course::instance($params['courseid']);
        self::validate_context($courseContext);

        require_capability('moodle/question:add', $courseContext);
        require_capability('moodle/question:editall', $courseContext);
        require_capability('moodle/question:managecategory', $courseContext);
        require_capability('moodle/question:moveall', $courseContext);
        require_capability('moodle/question:useall', $courseContext);

        [$defaultcategory, $contexts] = self::get_default_category_and_contexts($courseContext);

        $added_question_ids = self::import_questions_to_qbank($itemid, $defaultcategory, $params["courseid"], $contexts);

        if(count($added_question_ids) == 0){
            return ["status" => "ERROR", "error_message" => "No questions in file"];
        }
        
        return ['status' => 'OK', "num_of_questions" => count($added_question_ids)];
    }

    public static function upload_questions_returns(){
         return new external_single_structure([
            'status'           => new external_value(PARAM_TEXT, get_string('status_desc', 'local_qbankremotemanager')),
            'num_of_questions' => new external_value(PARAM_INT, get_string('num_of_questions_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL),
            'error_message'    => new external_value(PARAM_TEXT, get_string('error_message_desc', 'local_qbankremotemanager'), VALUE_OPTIONAL),
        ]);
    }

    /**
     * Helper function used to retrieve default category and contexts
     * 
     * @param object $context course context
     * 
     * @return array [0] = default category
     *               [1] = question edit contexts
    */
    private static function get_default_category_and_contexts($context){
        $contexts = new question_edit_contexts($context);

        $defaultcategory = question_make_default_categories($contexts->all());  

        return [$defaultcategory, $contexts];
    }

    /**
     * Helper function to import questions to question bank.
     * 
     * @param int $itemid id of the file in the draft area
     * @param object $defaultcategory object retrieved from get_default_category_and_contexts()
     * @param int $courseid id the of the course you want to import questions to
     * @param object $contexts object retrieved from get_default_category_and_contexts()
     * 
     * @return array of ids of newly imported questions
     */
    private static function import_questions_to_qbank($itemid, $defaultcategory, $courseid, $contexts){
        global $DB;

        qbank_helper::require_plugin_enabled('qbank_importquestions');
       
        $file = self::get_draft_file($itemid);

        $tempfolder = make_request_directory();
        $filename = $file->get_filename();
        $realFileName = $tempfolder . '/' . $filename;
        $file->copy_content_to($realFileName);
        
        $course = new stdClass();
        $course->id = $courseid;
        
        $category = $DB->get_record("question_categories", ['id' => $defaultcategory->id]);
        $category->context = context::instance_by_id($category->contextid);

        $qformat = new qformat_xml();
        $qformat->setContexts($contexts);
        $qformat->setCategory($category);
        $qformat->setCourse($course);
        $qformat->setFilename($realFileName);
        $qformat->setRealfilename($filename);
        $qformat->setMatchgrades("error");
        $qformat->setCatfromfile(1);
        $qformat->setContextfromfile(1);
        $qformat->setStoponerror(1);

        // supress echo from importprocess function - its messing up API response 
        ob_start();
        $success = $qformat->importprocess();
        ob_end_clean();

        if (!$success) {
            throw new moodle_exception('errorimportingquestions', 'local_qbankremotemanager');
        }

        return $qformat->questionids;
    }

    /**
     * Helper function used to retrieve the file from the draft area
     * 
     * @param int $itemid id of the file retrieved after upload the file to the draft area
     * 
     * @return object first file retrieved from the draft area with given id
     */
    private static function get_draft_file($itemid){
        global $USER;

        $fs = get_file_storage();

        $usercontext = context_user::instance($USER->id);

        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $itemid, 'id DESC', false);

        if (empty($files)){
            throw new moodle_exception('file_not_found', 'local_qbankremotemanager');
        }

        return reset($files);
    }

    /**
     * Function used to compute sum of defaultmarks of given questions
     * 
     * @param array $questions_ids - array of questions ids we want to get sum of defaultmark attribute
     * 
     * @return int sum of defaultmarks
    */
    private static function get_sum_of_default_question_grades($questions_ids){
        global $DB;

        if (empty($questions_ids)) {
            return 0;
        }

        $res = 0;

        $questions = $DB->get_records_list('question', 'id', $questions_ids, '', 'id, defaultmark');

        foreach ($questions as $q) {
            $res += $q->defaultmark;
        }

        return $res;
    }

    /**
     * Function used to add new test with given config to the course.
     * 
     * @param object $validated_config config from the user
     * @param object $course course you want to work with
    */
    private static function add_test($validated_config, $course){
        try {
            $saved_module = add_moduleinfo($validated_config, $course);
            return $saved_module->coursemodule;
        } catch (Exception $e) {
            throw new moodle_exception('error_adding_module', 'local_qbankremotemanager', '', $e->getMessage());
        }
    }

    /**
     * Funciont used to sanitize the config sent by the user
     * 
     * @param array $config retrieved from user
     * @param object $course we want to work with
     * 
     * @return object sanitized config
     */
    private static function prepare_quiz_data(array $config, $course) {
        global $DB;
        
        // we want to use system default value if the value was not set
        $quizconfig = get_config('quiz');
        
        $moduleinfo = new stdClass();

        $moduleinfo->modulename = 'quiz';
        $moduleinfo->module     = $DB->get_field('modules', 'id', array('name' => 'quiz'));
        $moduleinfo->course     = $course->id;
        
        $moduleinfo->decimalpoints         = self::validate_and_return_integer_value($config, "decimalpoints", $quizconfig->decimalpoints, 0, QUIZ_MAX_DECIMAL_OPTION);
        $moduleinfo->questiondecimalpoints = self::validate_and_return_integer_value($config, "questiondecimalpoints", $quizconfig->questiondecimalpoints, -1, QUIZ_MAX_Q_DECIMAL_OPTION);

        $moduleinfo->quizpassword = self::clean_validate_and_return_text_value($config, "quizpassword", "", PARAM_TEXT);
        
        $moduleinfo->visible             = self::validate_and_return_bool_value($config, "visible", 1);
        $moduleinfo->visibleoncoursepage = $moduleinfo->visible;


        $moduleinfo->name            = self::clean_validate_and_return_text_value($config, "quizname", "New quiz", PARAM_TEXT);
        $moduleinfo->intro           = self::clean_validate_and_return_text_value($config, "intro", "", PARAM_RAW);
        $moduleinfo->introformat     = FORMAT_HTML;
        $moduleinfo->showdescription =  self::validate_and_return_bool_value($config, 'showdescription', 0);

        $moduleinfo->timeopen  = (int)($config["timeopen"] ?? 0);
        $moduleinfo->timeclose = (int)($config["timeclose"] ?? 0);
        if ($moduleinfo->timeclose > 0 && $moduleinfo->timeclose < $moduleinfo->timeopen) {
            throw new moodle_exception('invalid_argument', 'local_qbankremotemanager', '', 'Time for closing must be after the time for opening.');
        }

        $moduleinfo->timelimit       = (int)($config["timelimit"] ?? $quizconfig->timelimit);
        $moduleinfo->timelimitenable =  $moduleinfo->timelimit > 0;

        $valid_overdue_handling_values = array_keys(quiz_get_overdue_handling_options());
        $moduleinfo->overduehandling   = self::clean_validate_and_return_text_value($config, "overduehandling", $quizconfig->overduehandling, PARAM_TEXT, $valid_overdue_handling_values);
        
        $moduleinfo->graceperiod = (int)($config["graceperiod"] ?? $quizconfig->graceperiod);

        $valid_grade_cats        = grade_get_categories_menu($course->id);
        $default_grade_cat       = reset($valid_grade_cats);
        $user_selected_grade_cat = self::clean_validate_and_return_text_value($config, "gradecat", $default_grade_cat, PARAM_TEXT, $valid_grade_cats);
        $moduleinfo->gradecat    = array_search($user_selected_grade_cat, $valid_grade_cats);

        $moduleinfo->attempts    = self::validate_and_return_integer_value($config, "attempts", 1, 0, QUIZ_MAX_ATTEMPT_OPTION);

        $valid_grade_methods        = quiz_get_grading_options();
        $valid_grade_keys           = array_values($valid_grade_methods);
        $user_selected_grade_method = self::clean_validate_and_return_text_value($config, "grademethod", $quizconfig->grademethod, PARAM_TEXT, $valid_grade_keys);
        
        // the existence in the array is validated in the clean_validate_and_return_text_value function a we need to incremenet one because this is one-based indexing
        $grademethod_key            = array_search($user_selected_grade_method, $valid_grade_keys) + 1;

        $moduleinfo->grademethod = $grademethod_key;

        $all_question_per_page_options = quiz_questions_per_page_options();
        $moduleinfo->questionsperpage  = self::validate_and_return_integer_value($config, "questionsperpage", $quizconfig->questionsperpage, 0, count($all_question_per_page_options) - 1);
        
        $all_nav_methods = array_keys(quiz_get_navigation_options());

        $moduleinfo->navmethod = self::clean_validate_and_return_text_value($config, "navmethod", $quizconfig->navmethod, PARAM_TEXT, $all_nav_methods);

        $moduleinfo->shuffleanswers = self::validate_and_return_bool_value($config, "shuffleanswers", $quizconfig->shuffleanswers);

        $available_preffered_behaviours = array_keys(question_engine::get_behaviour_options(''));
        $moduleinfo->preferredbehaviour = self::clean_validate_and_return_text_value($config, "preferredbehaviour", $quizconfig->preferredbehaviour, PARAM_TEXT, $available_preffered_behaviours);

        $moduleinfo->canredoquestions = self::validate_and_return_bool_value($config, "canredoquestions", $quizconfig->canredoquestions);

        $moduleinfo->attemptonlast = self::validate_and_return_bool_value($config, "attemptonlast", $quizconfig->attemptonlast);

        $available_user_image_options = quiz_get_user_image_options();
        $available_user_image_values = array_values($available_user_image_options);
        $defaul_user_image_option = $available_user_image_options[$quizconfig->showuserpicture];
        $user_selected_value = self::clean_validate_and_return_text_value($config, "showuserpicture", $defaul_user_image_option, PARAM_TEXT, $available_user_image_values);
        $key = array_search($user_selected_value, $available_user_image_options);
        $moduleinfo->showuserpicture = $key;
        
        $moduleinfo->allowofflineattempts = 0;

        $max_section_number = course_format::instance($course)->get_last_section_number();
        $min_section_number = 0;
        $moduleinfo->section = self::validate_and_return_integer_value($config, "section", 0, $min_section_number, $max_section_number);

        $gradepass_from_user = (float)($config["gradepass"] ?? 0.0);

        if ($gradepass_from_user < 0) {
            throw new moodle_exception("gradepass_too_low", 'local_qbankremotemanager');
        }

        $moduleinfo->gradepass = $gradepass_from_user;

       $review_fields = [
            'attempt',
            'correctness',
            'maxmarks',
            'marks',
            'specificfeedback',
            'generalfeedback',
            'rightanswer',
            'overallfeedback'
        ];

        $states = [
            'during', 
            'immediately', 
            'open', 
            'closed'
        ];

        foreach ($review_fields as $moodle_field => $client_suffix) {
            foreach ($states as $state_name) {
                $client_key = $client_suffix . $state_name;

                $user_value = (int)($config[$client_key] ?? 0);

                $moduleinfo->$client_key = $user_value;
            }
        }
       
        $moduleinfo->groupmode = 0;

        $browser_security = clean_param($config["browsersecurity"], PARAM_TEXT);

        $access_manager_exists = class_exists("mod_quiz\access_manager");
        if ($access_manager_exists){
            $browser_sec_values = array_keys(access_manager::get_browser_security_choices());
            $moduleinfo->browsersecurity = self::clean_validate_and_return_text_value($config, "browsersecurity", '-', PARAM_TEXT, $browser_sec_values);
        }
        else {
            //needed for quiz_access_manager in versions older than 4.2
            require_once("$CFG->dirroot/mod/quiz/accessmanager.php");
            
            $older_access_manager_exists = class_exists("quiz_access_manager");
            if ($older_access_manager_exists){
                $browser_sec_values = array_keys(\quiz_access_manager::get_browser_security_choices());
                $moduleinfo->browsersecurity = self::clean_validate_and_return_text_value($config, "browsersecurity", '-', PARAM_TEXT, $browser_sec_values);
            }
            else{
                throw new moodle_exception('browser_security_not_available', 'local_qbankremotemanager', '', 'It is not possible to retrieve browser security parameters');
            }
        }

        $moduleinfo->seb_requiresafeexambrowser = 0;
        $moduleinfo->cmidnumber = "";

        return $moduleinfo;
    }

    /**
     * Helper function used to validate integer value from quiz config.
     * 
     * @param object $config whole config from user
     * @param string $key key for the value we want to validate
     * @param int $default default value to use when value with given $key is not in $config
     * @param int $min min valid value
     * @param int $max max valid value
     * 
     * @return int validated config value
    */
    private static function validate_and_return_integer_value($config, $key, $default, $min, $max){
        $config_value = (int)($config[$key] ?? $default);
        
        if ($config_value < $min || $config_value > $max) {
            $errorData = new stdClass();
            $errorData->key = $key;
            $errorData->max = $max;
            $errorData->min = $min;

            throw new moodle_exception('badargument_with_range', 'local_qbankremotemanager', '', $errorData, "Invalid value for $key. Value has to be between $min and $max but was $config_value");
        }

        return $config_value;
    }

    /**
     * Helper function used to validate bool values from quiz config.
     * 
     * @param object $config whole config from user
     * @param string $key key for the value we want to validate
     * @param int $default default value to use when given key is undefined in $config 
     * 
     * @return int validated value (1 or 0)
    */
    private static function validate_and_return_bool_value($config, $key, $default){
        if ($default != 1 && $default != 0) {
            throw new moodle_exception('internal_error', 'local_qbankremotemanager');
        }

        $config_value = (int)($config[$key] ?? $default);

        if ($config_value != 0 && $config_value != 1){
            $errorData = new stdClass();
            $errorData->key = $key;

            throw new moodle_exception('badargument_bool', 'local_qbankremotemanager', '', $errorData, "Invalid value for $key. Expected 1 or 0");
        }

        return $config_value;
    }

    /**
     * Helper function to clean and validate other values from quiz config
     * 
     * @param object $config whole config from user
     * @param string $key key for the value we want to validate
     * @param int $default default value to use when given key is undefined in $config 
     * @param constant $expected_type use one of the PARAM_TEXT, PARAM_INT etc. Used in clean_param function.
     * @param array $valid_values array of expected values. Optional, if none passed only clean_param function validates the value.
     * 
     * @return string validated config value
     */
    private static function clean_validate_and_return_text_value($config, $key, $default, $expected_type, $valid_values = []){
        $config_value = clean_param($config[$key] ?? $default, $expected_type);

        if (count($valid_values) > 0 && !in_array($config_value, $valid_values)){
            $expected = "[";

            foreach ($valid_values as $val){
                $expected .= " $val,";
            }

            $expected .= "]";

            $errorData = new stdClass();
            $errorData->key = $key;
            $errorData->expected = $expected;
            $errorData->actual = $config_value;

            throw new moodle_exception('badargument_with_expected_values', 'local_qbankremotemanager', '', $errorData, "Invalid value for $key. Expected one of: $expected but was $config_value");
        }

        return $config_value;
    }

    /**
     * Helper function to add questions to quiz.
     * 
     * @param int $cmid course module id
     * @param array $question_ids array of question ids we want to add to quiz
     * @param object $course course we are working in
    */
    private static function add_questions_to_quiz($cmid, $question_ids, $course){
        list($quiz, $cm) = get_module_from_cmid($cmid);

        $quiz_settings_exists = class_exists("mod_quiz\quiz_settings");

        if($quiz_settings_exists){
            $quizobj = new quiz_settings($quiz, $cm, $course);
            $gradecalculator = $quizobj->get_grade_calculator();

            self::add_questions($question_ids, $quiz);
            
            $gradecalculator->recompute_quiz_sumgrades();
        }
        else{ //in older versions (< 4.2) class quiz settings doesn't exists, so we use older way of adding questions and updatings grades
            self::add_questions($question_ids, $quiz);
            quiz_delete_previews($quiz);
            quiz_update_sumgrades($quiz);
        }
    }

    /**
     * Helper function to add question to given quiz
     * 
     * @param array $question_ids list of question ids we want to include in given quiz
     * @param object $quiz quiz we want to import questions to
    */
    private static function add_questions($question_ids, $quiz){
        foreach($question_ids as $question_id){
            quiz_require_question_use($question_id);
            quiz_add_quiz_question($question_id, $quiz, 0);
        }
    }
}