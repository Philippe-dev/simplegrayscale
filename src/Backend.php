<?php
/**
 * @brief Simple Grayscale, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @author Start Bootstrap and Philippe aka amalgame
 *
 * @copyright Philippe Hénaff philippe@dissitou.org
 * @copyright GPL-2.0
 */
declare(strict_types=1);

namespace Dotclear\Theme\simplegrayscale;

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;
use Dotclear\Core\Backend\Page;

class Backend
{
    use TraitProcess;
    
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }
        if (defined('DC_CONTEXT_ADMIN')) {
            App::behavior()->addBehavior('adminPageHTMLHead', function () {
                if (App::blog()->settings->system->theme !== My::id()) {
                    return;
                }

                echo
                My::jsLoad('admin.js') . "\n" .
                My::jsLoad('popup_media.js') . "\n" .
                My::jsLoad('fontawesome.js') . "\n" .
                My::cssLoad('admin.css') . "\n" ;

                App::auth()->user_prefs->addWorkspace('accessibility');
                if (!App::auth()->user_prefs->accessibility->nodragdrop) {
                    echo
                    Page::jsLoad('js/jquery/jquery-ui.custom.js') .
                    Page::jsLoad('js/jquery/jquery.ui.touch-punch.js');
                }
            });
        }

        return true;
    }
}
