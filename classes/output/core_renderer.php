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
}
