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

namespace themes\simplegrayscale;

if (!defined('DC_RC_PATH')) {
    return;
}

l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/main');

# Simple Grayscale random image CSS and js files
$core->addBehavior('publicHeadContent', ['simpleGrayscalePublic','publicHeadContent']);
$core->addBehavior('publicFooterContent', ['simpleGrayscalePublic','publicFooterContent']);

# stickers
$core->tpl->addValue('simpleGrayscaleSocialLinks', ['simpleGrayscalePublic', 'simpleGrayscaleSocialLinks']);

# Simple menu template functions
$core->tpl->addValue('simpleGrayscaleSimpleMenu', ['tplSimpleGrayscaleSimpleMenu', 'simpleGrayscaleSimpleMenu']);

class simpleGrayscalePublic
{
    public static function publicHeadContent($core)
    {
        $core = $GLOBALS['core'];
        $_ctx = $GLOBALS['_ctx'];

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

        if (!isset($sb['use-featuredMedia'])) {
            $sb['use-featuredMedia'] = 0;
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

        for ($i = 0; $i < 6; $i++) {
            if (!isset($si['random-image-' . $i . '-url'])) {
                $si['random-image-' . $i . '-url'] = $theme_url . '/img/bg-intro-' . $i . '.jpg';
            }
            if (!isset($si['random-image-' . $i . '-tb-url'])) {
                $si['random-image-' . $i . '-tb-url'] = $theme_url . '/img/.bg-intro-' . $i . '_s.jpg';
            }
        }

        # check if post has featured media
        if ($_ctx->posts !== null && $core->plugins->moduleExists('featuredMedia')) {
            $_ctx->featured = new ArrayObject($core->media->getPostMedia($_ctx->posts->post_id, null, "featured"));
            foreach ($_ctx->featured as $featured_i => $featured_f) {
                $GLOBALS['featured_i'] = $featured_i;
                $GLOBALS['featured_f'] = $featured_f;
            }
            if (isset($featured_f->file_url)) {
                $featuredImageUrl = $featured_f->file_url;
            }
        }

        $rs = '<style>';
        if ($sb['use-featuredMedia'] && !empty($featuredImageUrl)) {
            $rs .= '.intro { background-image: url("' . $featuredImageUrl . '"); }';
        } else {
            if ($sb['default-image']) {
                $rs .= '.intro { background-image: url("' . $si['default-image-url'] . '"); }';
            } else {
                for ($i = 0; $i < 6; $i++) {
                    $rs .= '.intro.round' . $i . ' {' .
                    'background: #555 url(' . $si['random-image-' . $i . '-url'] . ');' .
                    'background-size: cover;' .
                    'background-position: center;' .
                '}';
                }
                $rs .= '.intro { background-image: none; }';
            }
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
            var round = parseInt(Math.random()*6);
                $('header.intro').addClass('round'+round);
            });" .
        "</script>\n";
    }

    public static function simpleGrayscaleSocialLinks($attr)
    {
        global $core;
        # Social media links
        $res     = '';

        $s = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_stickers');
        $s = @unserialize($s);
            
        $s = array_filter($s, 'self::cleanSocialLinks');
                
        $count = 0;
        foreach ($s as $sticker) {
            $res .= self::setSocialLink($count, ($count == count($s)), $sticker['label'], $sticker['url'], $sticker['image']);
            $count++;
        }

        if ($res != '') {
            return $res;
        }
    }
    protected static function setSocialLink($position, $last, $label, $url, $image)
    {
        return '<li id="slink' . $position . '"' . ($last ? ' class="last"' : '') . '>' . "\n" .
            '<a class="btn btn-default btn-lg" title="' . $label . '" href="' . $url . '">' .
            ' <i class="' . $image . '"></i>' . $label .
            '</a>' . "\n" .
            '</li>' . "\n";
    }

    protected static function cleanSocialLinks($s)
    {
        if (is_array($s)) {
            if (isset($s['label']) && isset($s['url']) && isset($s['image'])) {
                if ($s['label'] != null && $s['url'] != null && $s['image'] != null) {
                    return true;
                }
            }
        }
        return false;
    }
}

class tplSimpleGrayscaleSimpleMenu
{
    # Template function
    public static function simpleGrayscaleSimpleMenu($attr)
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
