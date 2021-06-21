<?php
/**
 * @brief Simple Grayscale, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @author Philippe aka amalgame and contributors
 * @copyright GPL-2.0
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/admin');

if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
    $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
} else {
    $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
}

$standalone_config = (boolean) $core->themes->moduleInfo($core->blog->settings->system->theme, 'standalone_config');

# random or default image behavior
$sb = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_behavior');
$sb = @unserialize($sb);

if (!is_array($sb)) {
    $sb = [];
}

if (!isset($sb['default-image'])) {
    $sb['default-image'] = 1;
}

# default or user defined images settings
$si = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_images');
$si = @unserialize($si);

if (!is_array($si)) {
    $si = [];
}

if (!isset($si['default-image-url'])) {
    $si['default-image-url'] = $theme_url . '/img/intro-bg.jpg';
}

if (!isset($si['default-image-tb-url'])) {
    $si['default-image-tb-url'] = $theme_url . '/img/.intro-bg_s.jpg';
}

for ($i = 0; $i < 3; $i++) {
    if (!isset($si['random-image-' . $i . '-url'])) {
        $si['random-image-' . $i . '-url'] = $theme_url . '/img/intro-bg-' . $i . '.jpg';
    }

    if (!isset($si['random-image-' . $i . '-tb-url'])) {
        $si['random-image-' . $i . '-tb-url'] = $theme_url . '/img/.intro-bg-' . $i . '_s.jpg';
    }
}

// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

if (!empty($_POST)) {
    try {
        # random or default image behavior
        $sb['default-image'] = $_POST['default-image'];

        # default image setting
        if (!empty($_POST['default-image-url'])) {
            $si['default-image-url'] = $_POST['default-image-url'];
        } else {
            $si['default-image-url'] = $theme_url . '/img/intro-bg.jpg';
        }

        # default image thumbnail settings
        if (!empty($_POST['default-image-tb-url'])) {
            $si['default-image-tb-url'] = $_POST['default-image-tb-url'];
        } else {
            $si['default-image-tb-url'] = $theme_url . '/img/.intro-bg_s.jpg';
        }

        
        for ($i = 0; $i < 3; $i++) {
            # random images settings
            if (!empty($_POST['random-image-' . $i . '-url'])) {
                $si['random-image-' . $i . '-url'] = $_POST['random-image-' . $i . '-url'];
            } else {
                $si['random-image-' . $i . '-url'] = $theme_url . '/img/intro-bg-' . $i . '.jpg';
            }

            # random images thumbnail settings
            if (!empty($_POST['random-image-' . $i . '-tb-url'])) {
                $si['random-image-' . $i . '-tb-url'] = $_POST['random-image-' . $i . '-tb-url'];
            } else {
                $si['random-image-' . $i . '-tb-url'] = $theme_url . '/img/.intro-bg-' . $i . '_s.jpg';
            }
        }

        $core->blog->settings->addNamespace('themes');
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_behavior', serialize($sb));
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_images', serialize($si));

        // Blog refresh
        $core->blog->triggerBlog();

        // Template cache reset
        $core->emptyTemplatesCache();

        dcPage::success(__('Theme configuration upgraded.'), true, true);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

// Legacy mode
if (!$standalone_config) {
    echo '</form>';
}
    echo '<form id="theme_config" action="' . $core->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

    echo '<h3>' . __('Behavior') . '</h3>';

    echo '<h4 class="pretty-title">' . __('Main background image') . '</h4>';

    echo '<p><label class="classic" for="default-image-1">' .
    form::radio(['default-image','default-image-1'], true, $sb['default-image']) .
    __('default image') . '</label></p>' .
    '<p><label class="classic" for="default-image-2">' .
    form::radio(['default-image','default-image-2'], false, !$sb['default-image']) .
    __('random image') . '</label></p>';

    echo '<h3>' . __('Images') . '</h3>';

    echo '<p class="form-note info maximal">' . __('Image dimensions should be at least 1200*450px. Only the <em>original</em> size can be selected.') . '</p> ';

    echo '<h4 class="pretty-title">' . __('Default image') . '</h4>';

    echo '<div class="box theme">';

    echo '<p> ' .
    '<img id="default-image-thumb-src" alt="' . __('Thumbnail') . '" src="' . $si['default-image-tb-url'] . '" width="240" height="90" />' .
    '</p>';

    echo '<p class="grayscale-buttons"><button type="button" id="default-image-selector">' . __('Change') . '</button>' .
    '<button class="delete" type="button" id="default-image-selector-reset">' . __('Reset') . '</button>' .
    '</p>' ;

    echo '<p class="sr-only">' . form::field('default-image-url', 30, 255, $si['default-image-url']) . '</p>';
    echo '<p class="sr-only">' . form::field('default-image-tb-url', 30, 255, $si['default-image-tb-url']) . '</p>';
    

    echo '</div>';

    echo '<h4 class="pretty-title">' . __('Random images') . '</h4>';

    for ($i = 0; $i < 3; $i++) {
        echo '<div class="box theme">';

        echo '<p> ' .
        '<img id="random-image-' . $i . '-thumb-src" alt="' . __('Thumbnail') . '" src="' . $si['random-image-' . $i . '-tb-url'] . '" width="240" height="90" />' .
        '</p>';

        echo '<p class="grayscale-buttons"><button type="button" id="random-image-' . $i . '-selector">' . __('Change') . '</button>' .
        '<button class="delete" type="button" id="random-image-' . $i . '-selector-reset">' . __('Reset') . '</button>' . '</p>' ;

        echo '<p class="sr-only">' . form::field('random-image-' . $i . '-url', 30, 255, $si['random-image-' . $i . '-url']) . '</p>';
        echo '<p class="sr-only">' . form::field('random-image-' . $i . '-tb-url', 30, 255, $si['random-image-' . $i . '-tb-url']) . '</p>';

        echo '</div>';
    }

    echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
    echo form::hidden(['theme-url'], $theme_url);
    echo form::hidden(['change-button-id'], '');
    echo '</form>';

dcPage::helpBlock('simplegrayscale');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
