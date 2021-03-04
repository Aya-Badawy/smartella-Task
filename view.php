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
 * pdf module version information
 *
 * @package mod_pdf
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/pdf/lib.php');
require_once($CFG->dirroot.'/mod/pdf/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // pdf instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$pdf = $DB->get_record('pdf', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('pdf', $pdf->id, $pdf->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('pdf', $id)) {
        print_error('invalidcoursemodule');
    }
    $pdf = $DB->get_record('pdf', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/pdf:view', $context);

// Completion and trigger events.
pdf_view($pdf, $course, $cm, $context);

$PAGE->set_url('/mod/pdf/view.php', array('id' => $cm->id));

$options = empty($pdf->displayoptions) ? array() : unserialize($pdf->displayoptions);

if ($inpopup and $pdf->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pdflayout('popup');
    $PAGE->set_title($course->shortname.': '.$pdf->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname.': '.$pdf->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($pdf);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($pdf->name), 2);
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($pdf->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'pdfintro');
        echo format_module_intro('pdf', $pdf, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($pdf->content, 'pluginfile.php', $context->id, 'mod_pdf', 'content', $pdf->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, $pdf->contentformat, $formatoptions);
echo $OUTPUT->box($content, "generalbox center clearfix");

if (!isset($options['printlastmodified']) || !empty($options['printlastmodified'])) {
    $strlastmodified = get_string("lastmodified");
    echo html_writer::div("$strlastmodified: " . userdate($pdf->timemodified), 'modified');
}

echo $OUTPUT->footer();
