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
 * List of all pdfs in course
 *
 * @package mod_pdf
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pdflayout('incourse');

// Trigger instances list viewed event.
$event = \mod_pdf\event\course_module_instance_list_viewed::create(array('context' => context_course::instance($course->id)));
$event->add_record_snapshot('course', $course);
$event->trigger();

$strpdf         = get_string('modulename', 'pdf');
$strpdfs        = get_string('modulenameplural', 'pdf');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/pdf/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strpdfs);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strpdfs);
echo $OUTPUT->header();
echo $OUTPUT->heading($strpdfs);
if (!$pdfs = get_all_instances_in_course('pdf', $course)) {
    notice(get_string('thereareno', 'moodle', $strpdfs), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($pdfs as $pdf) {
    $cm = $modinfo->cms[$pdf->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($pdf->section !== $currentsection) {
            if ($pdf->section) {
                $printsection = get_section_name($course, $pdf->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $pdf->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($pdf->timemodified)."</span>";
    }

    $class = $pdf->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed

    $table->data[] = array (
        $printsection,
        "<a $class href=\"view.php?id=$cm->id\">".format_string($pdf->name)."</a>",
        format_module_intro('pdf', $pdf, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
