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

/**
 * Form for editing HTML block instances.
 *
 * @package   block_course_info
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_info extends block_base
{

    function init()
    {
        $this->title = get_string('pluginname', 'block_course_info');
    }

    function has_config()
    {
        return false;
    }

    function applicable_formats()
    {
        return [
            'course-view' => true,
            'site' => true
        ];
    }

    function specialization()
    {
        if (isset($this->config->title)) {
            $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('newcourseinfoblock', 'block_course_info');
        }
    }

    function user_can_edit()
    {
        return true;
    }

    public function instance_can_be_hidden()
    {
        return false;
    }

    public function instance_can_be_collapsed()
    {
        return false;
    }

    function instance_allow_multiple()
    {
        return false;
    }

    function get_content()
    {
        global $CFG, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->title = get_string('pluginname', 'block_course_info');
        $courseid = context::instance_by_id($this->instance->parentcontextid)->instanceid;
        $idnumbers = explode(',', $DB->get_record('course', array('id' => $courseid))->idnumber);
        $daisyid = is_numeric(end($idnumbers)) ? trim(end($idnumbers)) : null;
        if ($daisyid) {
            $this->content->text = '<a href="https://daisy.dsv.su.se/servlet/Momentinfo?id=' . $daisyid . '" target="_blank">' .
                get_string('syllabus', 'block_course_info') . '</a><br/><a href="https://daisy.dsv.su.se/servlet/schema.moment.Momentschema?id=' .
                $daisyid . '" target="_blank">' . get_string('schedule', 'block_course_info') . '</a>';
        } else {
            return null;
        }
        return $this->content;
    }

    public function get_content_for_external($output)
    {
        global $CFG;
        require_once($CFG->libdir . '/externallib.php');

        $bc = new stdClass;
        $bc->title = null;
        $bc->content = '';
        $bc->contenformat = FORMAT_MOODLE;
        $bc->footer = '';
        $bc->files = [];

        if (!$this->hide_header()) {
            $bc->title = $this->title;
        }

        return $bc;
    }


    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false)
    {
        global $DB;

        $config = clone($data);
        $config->format = $data->text['format'];

        parent::instance_config_save($config, $nolongerused);
    }

    function instance_delete()
    {
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid)
    {
        $fromcontext = context_block::instance($fromid);
        return true;
    }

    function content_is_trusted()
    {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid, IGNORE_MISSING)) {
            return false;
        }
        //find out if this block is on the profile page
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                // this is exception - page is completely private, nobody else may see content there
                // that is why we allow JS here
                return true;
            } else {
                // no JS on public personal pages, it would be a big security issue
                return false;
            }
        }

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked()
    {
        return false;
    }

    /*
     * Add custom html attributes to aid with theming and styling
     *
     * @return array
     */
    function html_attributes()
    {
        global $CFG;

        $attributes = parent::html_attributes();

        return $attributes;
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external()
    {
        global $CFG;

        // Return all settings for all users since it is safe (no private keys, etc..).
        $instanceconfigs = !empty($this->config) ? $this->config : new stdClass();
        $pluginconfigs = (object)['allowcssclasses' => $CFG->block_course_info_allowcssclasses];

        return (object)[
            'instance' => $instanceconfigs,
            'plugin' => $pluginconfigs,
        ];
    }
}
