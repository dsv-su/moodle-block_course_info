<?php

defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_course_info/api_url', get_string('apiurl', 'block_course_info'), '', '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('block_course_info/api_user', get_string('apiuser', 'block_course_info'), '', '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('block_course_info/api_key', get_string('apikey', 'block_course_info'), '', '', PARAM_TEXT));
}