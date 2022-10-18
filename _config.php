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

l10n::set(dirname(__FILE__) . '/locales/' . dcCore::app()->lang . '/admin');

if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
    $theme_url = \http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
} else {
    $theme_url = \http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
}

$standalone_config = (bool) dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme, 'standalone_config');

// random or default image behavior
$sb = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_behavior');
$sb = $sb ? (unserialize($sb) ?: []) : [];

if (!is_array($sb)) {
    $sb = [];
}

if (!isset($sb['default-image'])) {
    $sb['default-image'] = 1;
}

// default or user defined images settings
$si = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_images');
$si = $si ? (unserialize($si) ?: []) : [];

if (!is_array($si)) {
    $si = [];
}

if (!isset($si['default-image-url'])) {
    $si['default-image-url'] = $theme_url . '/img/intro-bg.jpg';
}

if (!isset($si['default-image-tb-url'])) {
    $si['default-image-tb-url'] = $theme_url . '/img/.intro-bg_s.jpg';
}

for ($i = 0; $i < 6; $i++) {
    if (!isset($si['random-image-' . $i . '-url'])) {
        $si['random-image-' . $i . '-url'] = $theme_url . '/img/bg-intro-' . $i . '.jpg';
    }

    if (!isset($si['random-image-' . $i . '-tb-url'])) {
        $si['random-image-' . $i . '-tb-url'] = $theme_url . '/img/.bg-intro-' . $i . '_s.jpg';
    }
}

if (!isset($sb['use-featuredMedia'])) {
    $sb['use-featuredMedia'] = 0;
}

$stickers = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_stickers');
$stickers = $stickers ? (unserialize($stickers) ?: []) : [];

$stickers_full = [];
// Get all sticker images already used
if (is_array($stickers)) {
    foreach ($stickers as $v) {
        $stickers_full[] = $v['image'];
    }
}

// Get social media images
$stickers_images = ['fab fa-diaspora', 'fas fa-rss', 'fab fa-linkedin-in', 'fab fa-gitlab', 'fab fa-github', 'fab fa-twitter', 'fab fa-facebook-f',
    'fab fa-instagram', 'fab fa-mastodon', 'fab fa-pinterest', 'fab fa-snapchat', 'fab fa-soundcloud', 'fab fa-youtube', ];
if (is_array($stickers_images)) {
    foreach ($stickers_images as $v) {
        if (!in_array($v, $stickers_full)) {
            // image not already used
            $stickers[] = [
                'label' => null,
                'url'   => null,
                'image' => $v, ];
        }
    }
}

// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . dcCore::app()->lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . dcCore::app()->lang . '/resources.php';
}

$conf_tab = $_POST['conf_tab'] ?? 'presentation';

if (!empty($_POST)) {
    try {
        if ($conf_tab == 'presentation') {
            // random or default image behavior
            $sb['default-image'] = $_POST['default-image'];

            // use featured media for posts background images
            $sb['use-featuredMedia'] = (int) !empty($_POST['use-featuredMedia']);

            // default image setting
            if (!empty($_POST['default-image-url'])) {
                $si['default-image-url'] = $_POST['default-image-url'];
            } else {
                $si['default-image-url'] = $theme_url . '/img/intro-bg.jpg';
            }

            // default image thumbnail settings
            if (!empty($_POST['default-image-tb-url'])) {
                $si['default-image-tb-url'] = $_POST['default-image-tb-url'];
            } else {
                $si['default-image-tb-url'] = $theme_url . '/.intro-bg_s.jpg';
            }

            for ($i = 0; $i < 6; $i++) {
                // random images settings
                if (!empty($_POST['random-image-' . $i . '-url'])) {
                    $si['random-image-' . $i . '-url'] = $_POST['random-image-' . $i . '-url'];
                } else {
                    $si['random-image-' . $i . '-url'] = $theme_url . '/img/bg-intro-' . $i . '.jpg';
                }

                // random images thumbnail settings
                if (!empty($_POST['random-image-' . $i . '-tb-url'])) {
                    $si['random-image-' . $i . '-tb-url'] = $_POST['random-image-' . $i . '-tb-url'];
                } else {
                    $si['random-image-' . $i . '-tb-url'] = $theme_url . '/img/.bg-intro-' . $i . '_s.jpg';
                }
            }
        }

        if ($conf_tab == 'links') {
            $stickers = [];
            for ($i = 0; $i < count($_POST['sticker_image']); $i++) {
                $stickers[] = [
                    'label' => $_POST['sticker_label'][$i],
                    'url'   => $_POST['sticker_url'][$i],
                    'image' => $_POST['sticker_image'][$i],
                ];
            }

            $order = [];
            if (empty($_POST['ds_order']) && !empty($_POST['order'])) {
                $order = $_POST['order'];
                asort($order);
                $order = array_keys($order);
            }
            if (!empty($order)) {
                $new_stickers = [];
                foreach ($order as $i => $k) {
                    $new_stickers[] = [
                        'label' => $stickers[$k]['label'],
                        'url'   => $stickers[$k]['url'],
                        'image' => $stickers[$k]['image'],
                    ];
                }
                $stickers = $new_stickers;
            }
        }

        dcCore::app()->blog->settings->addNamespace('themes');
        dcCore::app()->blog->settings->themes->put(dcCore::app()->blog->settings->system->theme . '_behavior', serialize($sb));
        dcCore::app()->blog->settings->themes->put(dcCore::app()->blog->settings->system->theme . '_images', serialize($si));
        dcCore::app()->blog->settings->themes->put(dcCore::app()->blog->settings->system->theme . '_stickers', serialize($stickers));

        // Blog refresh
        dcCore::app()->blog->triggerBlog();

        // Template cache reset
        dcCore::app()->emptyTemplatesCache();

        dcPage::success(__('Theme configuration upgraded.'), true, true);
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
}

// Legacy mode
if (!$standalone_config) {
    echo '</form>';
}

echo '<div class="multi-part" id="themes-list' . ($conf_tab == 'presentation' ? '' : '-presentation') . '" title="' . __('Presentation') . '">';

echo '<form id="theme_config" action="' . dcCore::app()->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

echo '<div class="fieldset">';

echo '<h3>' . __('Background image') . '</h3>';

echo '<p><label class="classic" for="default-image-1">' .
form::radio(['default-image', 'default-image-1'], true, $sb['default-image']) .
__('default image') . '</label></p>' .
'<p><label class="classic" for="default-image-2">' .
form::radio(['default-image', 'default-image-2'], false, !$sb['default-image']) .
__('random image') . '</label></p>';

if (dcCore::app()->plugins->moduleExists('featuredMedia')) {
    echo '<p class="vertical-separator"><label class="classic" for="use-featuredMedia">' .
        form::checkbox('use-featuredMedia', '1', $sb['use-featuredMedia']) .
        __('Use featured media for posts') . '</label></p>';
}

echo '</div>';

echo '<div class="fieldset">';

echo '<h3>' . __('Images choice') . '</h3>';

echo '<h4 class="pretty-title">' . __('Default image') . '</h4>';

echo '<div class="box theme">';

echo '<p> ' .
'<img id="default-image-thumb-src" alt="' . __('Thumbnail') . '" src="' . $si['default-image-tb-url'] . '" width="240" height="90" />' .
'</p>';

echo '<p class="simplegrayscale-buttons"><button type="button" id="default-image-selector">' . __('Change') . '</button>' .
'<button class="delete" type="button" id="default-image-selector-reset">' . __('Reset') . '</button>' .
'</p>' ;

echo '<p class="sr-only">' . form::field('default-image-url', 30, 255, $si['default-image-url']) . '</p>';
echo '<p class="sr-only">' . form::field('default-image-tb-url', 30, 255, $si['default-image-tb-url']) . '</p>';

echo '</div>';

echo '<h4 class="pretty-title">' . __('Random images') . '</h4>';

for ($i = 0; $i < 6; $i++) {
    echo '<div class="box theme">';

    echo '<p> ' .
    '<img id="random-image-' . $i . '-thumb-src" alt="' . __('Thumbnail') . '" src="' . $si['random-image-' . $i . '-tb-url'] . '" width="240" height="90" />' .
    '</p>';

    echo '<p class="simplegrayscale-buttons"><button type="button" id="random-image-' . $i . '-selector">' . __('Change') . '</button>' .
    '<button class="delete" type="button" id="random-image-' . $i . '-selector-reset">' . __('Reset') . '</button>' . '</p>' ;

    echo '<p class="sr-only">' . form::field('random-image-' . $i . '-url', 30, 255, $si['random-image-' . $i . '-url']) . '</p>';
    echo '<p class="sr-only">' . form::field('random-image-' . $i . '-tb-url', 30, 255, $si['random-image-' . $i . '-tb-url']) . '</p>';

    echo '</div>';
}

echo '</div>';
echo '<p><input type="hidden" name="conf_tab" value="presentation" /></p>';
echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . dcCore::app()->formNonce() . '</p>';
echo form::hidden(['theme-url'], $theme_url);
echo form::hidden(['change-button-id'], '');
echo '</form>';
echo '</div>'; // Close tab

echo '<div class="multi-part" id="themes-list' . ($conf_tab == 'links' ? '' : '-links') . '" title="' . __('Stickers') . '">';
echo '<form id="theme_config" action="' . dcCore::app()->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

echo '<div class="fieldset">';

echo '<h4 class="pretty-title">' . __('Social links (footer)') . '</h4>';

echo
'<div class="table-outer">' .
'<table class="dragable">' . '<caption class="sr-only">' . __('Social links (footer)') . '</caption>' .
'<thead>' .
'<tr>' .
'<th scope="col">' . '</th>' .
'<th scope="col">' . __('Image') . '</th>' .
'<th scope="col">' . __('Label') . '</th>' .
'<th scope="col">' . __('URL') . '</th>' .
    '</tr>' .
    '</thead>' .
    '<tbody id="stickerslist">';
$count = 0;
foreach ($stickers as $i => $v) {
    $count++;
    echo
    '<tr class="line" id="l_' . $i . '">' .
    '<td class="handle">' . form::number(['order[' . $i . ']'], [
        'min'     => 0,
        'max'     => count($stickers),
        'default' => $count,
        'class'   => 'position',
    ]) .
    form::hidden(['dynorder[]', 'dynorder-' . $i], $i) . '</td>' .
    '<td class="linkimg">' . form::hidden(['sticker_image[]'], $v['image']) . '<i class="' . $v['image'] . '" title="' . $v['label'] . '"></i> ' . '</td>' .
    '<td scope="row">' . form::field(['sticker_label[]', 'dsl-' . $i], 20, 255, $v['label']) . '</td>' .
    '<td>' . form::field(['sticker_url[]', 'dsu-' . $i], 40, 255, $v['url']) . '</td>' .
        '</tr>';
}
echo
    '</tbody>' .
    '</table></div>';
echo '</div>';
echo '<p><input type="hidden" name="conf_tab" value="links" /></p>';
echo '<p class="clear">' . form::hidden('ds_order', '') . '<input type="submit" value="' . __('Save') . '" />' . dcCore::app()->formNonce() . '</p>';
echo '</form>';

echo '</div>'; // Close tab

dcPage::helpBlock('simplegrayscale');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
