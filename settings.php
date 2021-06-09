<?php

/**
 * Plugin administration pages are defined here.
 *
 * @package     theme_fnde
 * @category    admin
 * @author      Rodrigo Mady - @rmady
 * @copyright   2021 FNDE
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingfnde', get_string('configtitle', 'theme_fnde'));
    $page     = new admin_settingpage('theme_fnde_general', get_string('generalsettings', 'theme_fnde'));

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_fnde/brandcolor';
    $title = get_string('brandcolor', 'theme_fnde');
    $description = get_string('brandcolor_desc', 'theme_fnde');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_fnde/scsspre',
        get_string('rawscsspre', 'theme_fnde'), get_string('rawscsspre_desc', 'theme_fnde'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_fnde/scss', get_string('rawscss', 'theme_fnde'),
        get_string('rawscss_desc', 'theme_fnde'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
