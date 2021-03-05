<?php
require('../../config.php');
require_once($CFG->dirroot.'/mod/pdf/lib.php');
require_once($CFG->dirroot.'/mod/pdf/locallib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once('private/edit_iframe_add_page.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // pdf instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);
$page=optional_param('page', 0, PARAM_INT); // to open pdf in iframe on specific page.

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
if($page){
  $pdf->content=edit_iframe_link($pdf->content,$page);
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
