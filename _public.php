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
if (!defined('DC_RC_PATH')) {
    return;
}

l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/main');

# Grayscale random image CSS and js files
$core->addBehavior('publicHeadContent', ['simpleGrayscalePublic','publicHeadContent']);
$core->addBehavior('publicFooterContent', ['simpleGrayscalePublic','publicFooterContent']);

# Simple menu template functions
$core->tpl->addValue('SimpleGrayscaleSimpleMenu', ['tplSimpleGrayscaleSimpleMenu', 'SimpleGrayscaleSimpleMenu']);

class simpleGrayscalePublic
{
    public static function publicHeadContent($core)
    {
        $core = $GLOBALS['core'];

        # Settings
        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }

        $sb = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_behavior');
        $sb = @unserialize($sb);

        if (!is_array($sb)) {
            $sb = [];
        }

        if (!isset($sb['default-image'])) {
            $sb['default-image'] = 1;
        }

        $si = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_images');
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

        $rs = '<style>';
        $rs .= '.intro { background-image: url("' . $si['default-image-url'] . '"); }';
        if ($sb['default-image'] != 1) {
            for ($i = 0; $i < 3; $i++) {
                $rs .= '.intro.round' . $i . ' {' .
                    'background: #555 url(' . $si['random-image-' . $i . '-url'] . ');' .
                    'background-size: cover;' .
                    'background-position: center;' .
                '}';
            }
            $rs .= '.intro { background-image: none; }';
        }
        $rs .= '</style>';
        echo $rs;
    }
    public static function publicFooterContent($core)
    {
        $core = $GLOBALS['core'];

        # Settings
        $sb = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_behavior');
        $sb = @unserialize($sb);

        if (!is_array($sb)) {
            $sb = [];
        }

        if (!isset($sb['default-image'])) {
            $sb['default-image'] = 1;
        }

        if ($sb['default-image'] == 1) {
            return;
        }
        echo
        '<script>' . "\n" .
            "$(document).ready(function() {
            var round = parseInt(Math.random()*3);
                $('header.intro').addClass('round'+round);
            });" .
        "</script>\n";
    }
}

class tplSimpleGrayscaleSimpleMenu
{
    # Template function
    public static function SimpleGrayscaleSimpleMenu($attr)
    {
        global $core;

        if (!(boolean) $core->blog->settings->system->simpleMenu_active) {
            return '';
        }

        $class       = isset($attr['class']) ? trim($attr['class']) : '';
        $id          = isset($attr['id']) ? trim($attr['id']) : '';
        $description = isset($attr['description']) ? trim($attr['description']) : '';

        if (!preg_match('#^(title|span|both|none)$#', $description)) {
            $description = '';
        }

        return '<?php echo tplSimpleGrayscaleSimpleMenu::displayMenu(' .
        "'" . addslashes($class) . "'," .
        "'" . addslashes($id) . "'," .
        "'" . addslashes($description) . "'" .
            '); ?>';
    }

    public static function displayMenu($class = '', $id = '', $description = '')
    {
        global $core;

        $ret = '';

        if (!(boolean) $core->blog->settings->system->simpleMenu_active) {
            return $ret;
        }

        $menu = $core->blog->settings->system->simpleMenu;
        if (is_array($menu)) {
            // Current relative URL
            $url     = $_SERVER['REQUEST_URI'];
            $abs_url = http::getHost() . $url;

            // Home recognition var
            $home_url       = html::stripHostURL($core->blog->url);
            $home_directory = dirname($home_url);
            if ($home_directory != '/') {
                $home_directory = $home_directory . '/';
            }

            // Menu items loop
            foreach ($menu as $i => $m) {
                # $href = lien de l'item de menu
                $href = $m['url'];
                $href = html::escapeHTML($href);

                # Cope with request only URL (ie ?query_part)
                $href_part = '';
                if ($href != '' && substr($href, 0, 1) == '?') {
                    $href_part = substr($href, 1);
                }

                $targetBlank = ((isset($m['targetBlank'])) && ($m['targetBlank'])) ? true : false;

                # Active item test
                $active = false;
                if (($url == $href) || ($abs_url == $href) || ($_SERVER['URL_REQUEST_PART'] == $href) || (($href_part != '') && ($_SERVER['URL_REQUEST_PART'] == $href_part)) || (($_SERVER['URL_REQUEST_PART'] == '') && (($href == $home_url) || ($href == $home_directory)))) {
                    $active = true;
                }
                $title = $span = '';

                if ($m['descr']) {
                    if (($description == 'title' || $description == 'both') && $targetBlank) {
                        $title = html::escapeHTML(__($m['descr'])) . ' (' .
                        __('new window') . ')';
                    } elseif ($description == 'title' || $description == 'both') {
                        $title = html::escapeHTML(__($m['descr']));
                    }
                    if ($description == 'span' || $description == 'both') {
                        $span = ' <span class="simple-menu-descr">' . html::escapeHTML(__($m['descr'])) . '</span>';
                    }
                }

                if (empty($title) && $targetBlank) {
                    $title = __('new window');
                }
                if ($active && !$targetBlank) {
                    $title = (empty($title) ? __('Active page') : $title . ' (' . __('active page') . ')');
                }

                $label = html::escapeHTML(__($m['label']));

                $item = new ArrayObject([
                    'url'    => $href,   // URL
                    'label'  => $label,  // <a> link label
                    'title'  => $title,  // <a> link title (optional)
                    'span'   => $span,   // description (will be displayed after <a> link)
                    'active' => $active, // status (true/false)
                    'class'  => ''      // additional <li> class (optional)
                ]);

                # --BEHAVIOR-- publicSimpleMenuItem
                $core->callBehavior('publicSimpleMenuItem', $i, $item);

                $ret .= '<li class="nav-item li' . ($i + 1) .
                    ($item['active'] ? ' active' : '') .
                    ($i == 0 ? ' li-first' : '') .
                    ($i == count($menu) - 1 ? ' li-last' : '') .
                    ($item['class'] ? ' ' . $item['class'] : '') .
                    '">' .
                    '<a class="nav-link js-scroll-trigger" href="' . $href . '"' .
                    (!empty($item['title']) ? ' title="' . $label . ' - ' . $item['title'] . '"' : '') .
                    (($targetBlank) ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' .
                    '<span class="simple-menu-label">' . $item['label'] . '</span>' .
                    $item['span'] . '</a>' .
                    '</li>';
            }
            // Final rendering
            if ($ret) {
                $ret = '<ul ' . ($id ? 'id="' . $id . '"' : '') . ' class="simple-menu' . ($class ? ' ' . $class : '') . '">' . "\n" . $ret . "\n" . '</ul>';
            }
        }

        return $ret;
    }
}
