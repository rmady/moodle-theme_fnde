<?php

/**
 * Lib file.
 *
 * @package     theme_fnde
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Load the main SCSS and the frontpage banner.
 *
 * @param theme_config $theme
 *            The theme config object.
 * @return string
 */
function theme_fnde_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $scss .= file_get_contents("$CFG->dirroot/theme/fnde/scss/defaultvariables.scss");
    $scss .= file_get_contents("$CFG->dirroot/theme/fnde/scss/fnde.scss");
    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme
 *            The theme config object.
 * @return string
 */
function theme_fnde_get_pre_scss($theme) {
    global $CFG;

    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['primary'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * MoodlePage init for adding classes to body tag.
 *
 * @param moodle_page $page
 * @throws coding_exception
 */
function theme_fnde_page_init(moodle_page $page) {
    global $COURSE, $USER;

    // Add admin classes.
    $page->add_body_class(is_siteadmin() ? "is_siteadmin" : "not_siteadmin");

    // Add module idnumber class.
    if (in_array($page->pagelayout, ['incourse']) && !empty($page->cm->idnumber)) {
        $page->add_body_class("idnumber-{$page->cm->idnumber}");
    }

    // Add role classes.
    if (in_array($page->pagelayout, ['course', 'incourse'])) {
        $context = context_course::instance($COURSE->id);
        if (user_has_role_assignment($USER->id, 5, $context->id)) {
            $page->add_body_class('is_student');
        }
        if (user_has_role_assignment($USER->id, 4, $context->id)) {
            $page->add_body_class('is_teacher');
        }
        if (user_has_role_assignment($USER->id, 3, $context->id)) {
            $page->add_body_class('is_editingteacher');
        }
    }

    // Load course style by shortname from: /style/course/$shortname.css.
    if ($COURSE->id > 1) {
        $shortname   = explode('|', $COURSE->shortname);
        $shortname   = trim($shortname[0]);
        $coursestyle = "/style/course/{$shortname}.css";
        if (file_exists($page->theme->dir.$coursestyle)) {
            $page->requires->css(new moodle_url("/theme/fnde{$coursestyle}"));
        }
    }
}