<?php
/**
 * Course renderer.
 *
 * @package     theme_fnde
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */
namespace theme_fnde\output\core;

defined('MOODLE_INTERNAL') || die();

use core_course_category;
use core_course_list_element;
use moodle_url;
use html_writer;
use coursecat_helper;
use stdClass;

/**
 * Class course_renderer.
 *
 * @package     theme_fnde
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */
class course_renderer extends \core_course_renderer {
    /**
     * Renders the list of courses for frontpage and /course
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;

        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit.
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // Prepare content of paging bar if it is needed.
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // There are more results that can fit on one page.
            if ($paginationurl) {
                // The option paginationurl was specified, display pagingbar.
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                    $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                        get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // The option for 'View more' link was specified, display more link.
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new \lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                    array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // There are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode.
            $pagingbar = html_writer::tag(
                'div',
                html_writer::link(
                    $paginationurl->out(
                        false,
                        array('perpage' => $CFG->coursesperpage)
                    ),
                    get_string('showperpage', '', $CFG->coursesperpage)
                ),
                array('class' => 'paging paging-showperpage')
            );
        }
        // Display list of courses.
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 1;
        $content .= html_writer::start_tag('div', array('class' => ' row card-deck my-4'));
        foreach ($courses as $course) {
            $content .= $this->coursecat_coursebox($chelper, $course, 'card mb-3 course-card-view');
            $coursecount ++;
        }

        $content .= html_writer::end_tag('div');
        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // End courses.
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = html_writer::start_tag('div', array('class' => $additionalclasses));
        $classes = '';
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h5';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }
        // End coursebox.
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div');
        // End coursebox.
        $content .= html_writer::end_tag('div');
        // End col-md-4.
        return $content;
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $OUTPUT;

        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        // Course name.
        $coursename = $chelper->get_course_formatted_name($course);
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
        $coursenamelink = html_writer::link($courseurl,
            $coursename, array('class' => $course->visible ? '' : 'dimmed'));

        $content = html_writer::start_tag('a', array ('href' => $courseurl, 'class' => 'course-card-img'));
        $content .= $this->get_course_summary_image($course);
        $content .= html_writer::end_tag('a');

        $content .= html_writer::start_tag('div', array('class' => 'card-body'));
        $content .= "<h4 class='card-title text-center m-1'>". $coursenamelink ."</h4>";
        $content .= html_writer::end_tag('div');

        $content .= html_writer::start_tag('div', array('class' => 'card-block text-center'));

        // Print enrolmenticons.
        if ($icons = enrol_get_course_info_icons($course)) {
            foreach ($icons as $pixicon) {
                $content .= $this->render($pixicon);
            }
        }

        $content .= html_writer::start_tag('div', array('class' => 'pull-right'));
        $content .= html_writer::end_tag('div'); // End pull-right.

        $content .= html_writer::end_tag('div'); // End card-block.

        // Don't display course contacts. See core_course_list_element::get_course_contacts().

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            if ($cat = core_course_category::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat text-center'));
                $content .= get_string('category').': '.
                    html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                        $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // End coursecat.
            }
        }

        // Display course summary.
        if ($course->has_summary()) {
            $summarytype = get_config('theme_fnde', 'summarytype');

            if ($summarytype == 'popover') {
                $content .= html_writer::start_tag('div', array('class' => 'card-see-more text-center'));
                $content .= html_writer::start_tag('div', array('class' => 'btn btn-secondary m-2',
                    'id' => "course-popover-{$course->id}", 'role' => 'button', 'data-region' => 'popover-region-toggle',
                    'data-toggle' => 'popover', 'data-placement' => 'right',
                    'data-content' => $chelper->get_course_formatted_summary($course, ['noclean' => true, 'para' => false]),
                    'data-html' => 'true', 'tabindex' => '0', 'data-trigger' => 'focus'));
                $content .= get_string('seemore', 'theme_fnde');
                $content .= html_writer::end_tag('div');
                $content .= html_writer::end_tag('div'); // End summary.
            } else if ($summarytype == 'modal') {
                $modal = [
                    'body' => $chelper->get_course_formatted_summary($course, ['overflowdiv' => true,
                        'noclean' => true, 'para' => false]),
                    'title' => format_text($course->fullname, FORMAT_HTML),
                    'uniqid' => $course->id,
                    'classes' => "modal-$course->id",
                    'courselink' => new moodle_url("/course/view.php", ['id' => $course->id])
                ];
                $content .= $OUTPUT->render_from_template('theme_fnde/course_summary_modal', $modal);
            }
        }

        return $content;
    }

    /**
     * Returns the first course's summary issue
     *
     * @param stdClass $course the course object
     * @return string
     */
    protected function get_course_summary_image($course) {
        global $CFG;

        $contentimage = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimage = html_writer::start_tag('div', array('style' => "background-image:url('$url')",
                    'class' => 'card-img-top'));
                $contentimage .= html_writer::end_tag('div');
                break;
            }
        }

        if (empty($contentimage)) {
            $pattern = new \core_geopattern();
            $pattern->setColor($this->coursecolor($course->id));
            $pattern->patternbyid($course->id);
            $contentimage = html_writer::start_tag('div', array('style' => "background-image:url('{$pattern->datauri()}')",
                'class' => 'card-img-top'));
            $contentimage .= html_writer::end_tag('div');
        }

        return $contentimage;
    }

    /**
     * Generate a semi-random color based on the courseid number (so it will always return
     * the same color for a course)
     *
     * @param int $courseid
     * @return string $color, hexvalue color code.
     */
    protected function coursecolor($courseid) {
        // The colour palette is hardcoded for now. It would make sense to combine it with theme settings.
        $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894', '#0984e3', '#b2bec3',
            '#fdcb6e', '#fd79a8', '#6c5ce7'];

        $color = $basecolors[$courseid % 10];
        return $color;
    }
}
