<?php
/**
 * Services definition for QBank Remote Manager plugin
 *
 * @package    local_qbankremotemanager
 * @copyright  2026 Peter Vanát <vanat.peter@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_qbankremotemanager_am_i_here' => [
        'classname'    => 'local_qbankremotemanager\external\qbankremotemanager_external',
        'methodname'   => 'am_i_here',
        'description'  => 'Tests if this plugin is available in LMS Moodle instance.',
        'type'         => 'read',        
        'ajax'         => true,
    ],
    'local_qbankremotemanager_upload_quiz' => [
        'classname'    => 'local_qbankremotemanager\external\qbankremotemanager_external',
        'methodname'   => 'upload_quiz',
        'description'  => 'Uploads new quiz to course with questions from file in Moodle XML format. This file must be uploaded beforehand.',
        'type'         => 'write',
        'capabilities' => 'moodle/question:add, moodle/question:editall, moodle/question:managecategory, moodle/question:moveall, moodle/question:useall, mod/quiz:addinstance, mod/quiz:manage',        
        'ajax'         => true,
    ],
    'local_qbankremotemanager_get_question_categories' => [
        'classname'    => 'local_qbankremotemanager\external\qbankremotemanager_external',
        'methodname'   => 'get_question_bank_categories',
        'description'  => 'Retrieve categories available for export from question bank.',
        'type'         => 'read',
        'capabilities' => 'moodle/question:editall, moodle/question:managecategory',        
        'ajax'         => true,
    ],
    'local_qbankremotemanager_upload_questions' => [
        'classname'    => 'local_qbankremotemanager\external\qbankremotemanager_external',
        'methodname'   => 'upload_questions',
        'description'  => 'Import new questions to questions bank from Moodle XML format. This file must be uploaded beforehand.',
        'type'         => 'write',
        'capabilities' => 'moodle/question:add, moodle/question:editall, moodle/question:managecategory, moodle/question:moveall, moodle/question:useall',        
        'ajax'         => true,
    ]
];

$services = [
    'QBank Remote Manager API' => [
        'functions' => [
            'local_qbankremotemanager_upload_quiz', 
            'local_qbankremotemanager_am_i_here', 
            'local_qbankremotemanager_get_question_categories', 
            'local_qbankremotemanager_upload_questions'
        ],
        'restrictedusers' => 0,
        'enabled'         => 1,
        'shortname'       => 'qbank_manager'
    ]
];
