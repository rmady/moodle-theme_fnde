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

    // Favicon image setting.
    $name = 'theme_fnde/favicon';
    $title = get_string('favicon', 'theme_fnde');
    $description = get_string('favicon_desc', 'theme_fnde');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // HTML to include in the footer content of frontpage.
    $footerhtml = '<div class="d-flex flex-wrap">
            <div class="flex-item">
                <h3>AVA/FNDE</h3>
                <ul>
                    <li><a href="#">Cursos disponíveis</a></li>
                    <li><a href="#">Meus cursos</a></li>
                    <li><a href="#">Alterar dados cadastrais</a></li>
                    <li><a href="#">Upload de arquivos</a></li>
                    <li><a href="#">Certificados</a></li>
                </ul>
            </div>

            <div class="flex-item">
                <h3>Links úteis</h3>
                <ul>
                    <li><a href="https://www.gov.br/fnde/pt-br" target="_blank">Portal FNDE</a></li>
                    <li><a href="https://www.fnde.gov.br/educacaocorporativa/" target="_blank">Portal Educação Corporativa</a></li>
                    <li><a href="https://www.fnde.gov.br/ava/" target="_blank">AVA/FNDE</a></li>
                </ul>
            </div>

            <div class="flex-item flex-grow-1">
                <div class="social-links">
                    <a class="social-item" target="_blank" href="https://www.facebook.com/fnde.educacao">
                        <span class="fa fa-facebook" aria-hidden="true"></span>
                        <span class="sr-only">Facebook</span>
                    </a>
                    <a class="social-item" target="_blank" href="https://www.youtube.com/channel/UCp3JfOII-BSbHqlijmUWg7A">
                        <span class="fa fa-youtube" aria-hidden="true"></span>
                        <span class="sr-only">Youtube</span>
                    </a>
                    <a class="social-item" target="_blank" href="https://www.instagram.com/fnde.oficial/">
                        <span class="fa fa-instagram" aria-hidden="true"></span>
                        <span class="sr-only">Instagram</span>
                    </a>
                </div>
                <p>&copy; FNDE. Todos os direitos reservados.</p>
            </div>
        </div>';
    $setting = new admin_setting_confightmleditor('theme_fnde/defaultfooter', get_string('defaultfooter', 'theme_fnde'),
        get_string('defaultfooter_desc', 'theme_fnde'), $footerhtml, PARAM_RAW);
    $page->add($setting);


    $settings->add($page);
}
