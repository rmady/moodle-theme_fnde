<?php

/**
 * Core renderer.
 *
 * @package     theme_fnde
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */

namespace theme_fnde\output;

use custom_menu;
use stdClass;
use moodle_url;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot."/course/format/lib.php");

/**
 * Class core_renderer.
 *
 * @package     theme_fnde
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Returns the url of the custom favicon.
     *
     * @return moodle_url|string
     */
    public function favicon() {
        $favicon = $this->page->theme->setting_file_url('favicon', 'favicon');

        if (empty($favicon)) {
            return $this->page->theme->image_url('favicon', 'theme');
        } else {
            return $favicon;
        }
    }

    /**
     * Always show the compact logo when its defined.
     *
     * @return bool
     */
    public function should_display_navbar_logo() {
        $logo = $this->get_compact_logo_url();
        return !empty($logo);
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {

        if ($this->page->include_region_main_settings_in_header_actions() &&
            !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();

        if (in_array($this->page->pagelayout, ['mydashboard'])) {
            return $this->render_from_template('theme_fnde/header', $header);
        } else {
            return $this->render_from_template('core/full_header', $header);
        }
    }

}
