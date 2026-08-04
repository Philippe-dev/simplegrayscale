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
use Dotclear\Helper\Html\Form\Button;
use Dotclear\Helper\Html\Form\Caption;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form as HtmlForm;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Img;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Radio;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Table;
use Dotclear\Helper\Html\Form\Tbody;
use Dotclear\Helper\Html\Form\Td;
use Dotclear\Helper\Html\Form\Th;
use Dotclear\Helper\Html\Form\Thead;
use Dotclear\Helper\Html\Form\Tr;
use Dotclear\Helper\Html\Form\Text;
use Exception;

class Config
{
    use TraitProcess;

    public static function init(): bool
    {
        // limit to backend permissions
        if (!self::status(My::checkContext(My::CONFIG))) {
            return false;
        }

        My::l10n('admin');

        App::backend()->standalone_config = (bool) App::themes()->moduleInfo(App::blog()->settings->system->theme, 'standalone_config');

        // Load contextual help
        App::themes()->loadModuleL10Nresources(My::id(), App::lang()->getLang());

        # random or default image behavior
        $behavior = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_behavior');
        $behavior = $behavior ? (unserialize($behavior) ?: []) : [];

        if (!is_array($behavior)) {
            $behavior = [];
        }

        if (!isset($behavior['default-image'])) {
            $behavior['default-image'] = 1;
        }

        # default or user defined images settings
        $images = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_images');
        $images = $images ? (unserialize($images) ?: []) : [];

        if (!is_array($images)) {
            $images = [];
        }

        if (!isset($images['default-image-url'])) {
            $images['default-image-url'] = My::fileURL('/img/intro-bg.jpg');
        }

        if (!isset($images['default-image-tb-url'])) {
            $images['default-image-tb-url'] = My::fileURL('/img/.intro-bg_s.jpg');
        }

        for ($i = 0; $i < 6; $i++) {
            if (!isset($images['random-image-' . $i . '-url'])) {
                $images['random-image-' . $i . '-url'] = My::fileURL('/img/bg-intro-' . $i . '.jpg');
            }

            if (!isset($images['random-image-' . $i . '-tb-url'])) {
                $images['random-image-' . $i . '-tb-url'] = My::fileURL('/img/.bg-intro-' . $i . '_s.jpg');
            }
        }

        if (!isset($behavior['use-featuredMedia'])) {
            $behavior['use-featuredMedia'] = 0;
        }

        $stickers = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_stickers');
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

        App::backend()->behavior = $behavior;
        App::backend()->images   = $images;
        App::backend()->stickers = $stickers;

        App::backend()->conf_tab = $_POST['conf_tab'] ?? 'images';

        return self::status();
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (!empty($_POST)) {
            try {
                $behavior = App::backend()->behavior ?? [];
                $images   = App::backend()->images     ?? [];

                // HTML
                if (App::backend()->conf_tab === 'images') {
                    # random or default image behavior
                    $behavior['default-image']     = $_POST['default-image'] ?? 1;
                    $behavior['use-featuredMedia'] = !empty($_POST['use-featuredMedia']) ? 1 : 0;
                    if (!empty($_POST['default-image-url'])) {
                        $images['default-image-url'] = $_POST['default-image-url'];
                    } else {
                        $images['default-image-url'] = My::fileURL('/img/intro-bg.jpg');
                    }

                    # default image thumbnail settings
                    if (!empty($_POST['default-image-tb-url'])) {
                        $images['default-image-tb-url'] = $_POST['default-image-tb-url'];
                    } else {
                        $images['default-image-tb-url'] = My::fileURL('/img/.intro-bg_s.jpg') . '/';
                    }

                    for ($i = 0; $i < 6; $i++) {
                        # random images settings
                        if (!empty($_POST['random-image-' . $i . '-url'])) {
                            $images['random-image-' . $i . '-url'] = $_POST['random-image-' . $i . '-url'];
                        } else {
                            $images['random-image-' . $i . '-url'] = My::fileURL('/img/bg-intro-' . $i . '.jpg');
                        }

                        # random images thumbnail settings
                        if (!empty($_POST['random-image-' . $i . '-tb-url'])) {
                            $images['random-image-' . $i . '-tb-url'] = $_POST['random-image-' . $i . '-tb-url'];
                        } else {
                            $images['random-image-' . $i . '-tb-url'] = My::fileURL('/img/.bg-intro-' . $i . '_s.jpg');
                        }
                    }

                    App::backend()->behavior = $behavior;
                    App::backend()->images   = $images;
                }

                if (App::backend()->conf_tab === 'stickers') {
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
                    App::backend()->stickers = $stickers;
                }
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_behavior', serialize(App::backend()->behavior));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_images', serialize(App::backend()->images));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_stickers', serialize(App::backend()->stickers));

                // Blog refresh
                App::blog()->triggerBlog();

                // Template cache reset
                App::cache()->emptyTemplatesCache();

                App::backend()->notices()->message(__('Theme configuration upgraded.'), true, true);
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        if (!App::backend()->standalone_config) {
            echo '</form>';
        }

        $default_image = isset(App::backend()->behavior['default-image']) ? (bool) App::backend()->behavior['default-image'] : true;

        echo
        (new Div('images'))
            ->class('multi-part')
            ->title(__('Images'))
            ->items([
                (new HtmlForm('theme_config'))
                    ->action(App::backend()->url()->get('admin.blog.theme', ['conf' => '1']))
                    ->method('post')
                    ->enctype('multipart/form-data')
                    ->fields([
                        (new Fieldset())
                            ->class('fieldset')
                            ->fields([
                                (new Para())->items([
                                    (new Radio(['default-image', 'default-image-1'], $default_image))
                                        ->value('1')
                                        ->label((new Label(__('default image'), Label::INSIDE_TEXT_AFTER))->class('classic')),
                                ]),
                                (new Para())->items([
                                    (new Radio(['default-image', 'default-image-2'], !$default_image))
                                        ->value('0')
                                        ->label((new Label(__('random image'), Label::INSIDE_TEXT_AFTER))->class('classic')),
                                ]),
                                ...(
                                    App::plugins()->moduleExists('featuredMedia') ? [
                                        (new Para())->class('vertical-separator')->items([
                                            (new Checkbox('use-featuredMedia', (bool) (App::backend()->behavior['use-featuredMedia'] ?? false)))
                                                ->value('1')
                                                ->label((new Label(__('Use featured media for posts'), Label::INSIDE_TEXT_AFTER))->class('classic')),
                                        ]),
                                    ] : []
                                ),
                            ]),
                        (new Fieldset())
                            ->class('fieldset')
                            ->legend((new Legend(__('Default image'))))
                            ->fields([
                                (new Div())
                                    ->class('box theme')
                                    ->items([
                                        (new Para())->items([
                                            (new Img('default-image-thumb-src'))
                                                ->id('default-image-thumb-src')
                                                ->alt(__('Thumbnail'))
                                                ->src(App::backend()->images['default-image-tb-url'])
                                                ->width(240)
                                                ->height(160),
                                        ]),
                                        (new Para())->class('simplegrayscale-buttons')->items([
                                            (new Button('default-image-selector', __('Change')))
                                                ->type('button')
                                                ->id('default-image-selector'),
                                            (new Button('default-image-selector-reset', __('Reset')))
                                                ->class('delete')
                                                ->type('button')
                                                ->id('default-image-selector-reset'),
                                        ]),
                                        (new Hidden('default-image-url'))
                                            ->value(App::backend()->images['default-image-url']),
                                        (new Hidden('default-image-tb-url'))
                                            ->value(App::backend()->images['default-image-tb-url']),
                                    ]),
                            ]),
                        (new Fieldset())
                            ->class('fieldset')
                            ->legend((new Legend(__('Random images'))))
                            ->fields(array_map(function (int $i) {
                                return (new Div())
                                    ->class('box theme')
                                    ->items([
                                        (new Para())->items([
                                            (new Img('random-image-' . $i . '-thumb-src'))
                                                ->id('random-image-' . $i . '-thumb-src')
                                                ->alt(__('Thumbnail'))
                                                ->src(App::backend()->images['random-image-' . $i . '-tb-url'])
                                                ->width(240)
                                                ->height(160),
                                        ]),
                                        (new Para())->class('simplegrayscale-buttons')->items([
                                            (new Button('random-image-' . $i . '-selector', __('Change')))
                                                ->type('button')
                                                ->id('random-image-' . $i . '-selector'),
                                            (new Button('random-image-' . $i . '-selector-reset', __('Reset')))
                                                ->class('delete')
                                                ->type('button')
                                                ->id('random-image-' . $i . '-selector-reset'),
                                        ]),
                                        (new Hidden('random-image-' . $i . '-url'))
                                            ->value(App::backend()->images['random-image-' . $i . '-url']),
                                        (new Hidden('random-image-' . $i . '-tb-url'))
                                            ->value(App::backend()->images['random-image-' . $i . '-tb-url']),
                                    ]);
                            }, range(0, 5))),
                        (new Para())->items([
                            (new Hidden('conf_tab'))
                                ->value('images'),
                        ]),
                        (new Para())->class('clear')->items([
                            (new Submit(['images'], __('Save'))),
                            App::nonce()->formNonce(),
                        ]),
                        (new Hidden('theme-url'))
                            ->value(My::fileURL('')),
                        (new Hidden('change-button-id'))
                            ->value(''),
                    ]),
            ])
        ->render();

        echo
        (new Div('stickers'))
            ->class('multi-part')
            ->title(__('Stickers'))
            ->items([
                (new HtmlForm('theme_config'))
                    ->action(App::backend()->url()->get('admin.blog.theme', ['conf' => '1']))
                    ->method('post')
                    ->enctype('multipart/form-data')
                    ->fields([
                        (new Fieldset())
                            ->class('fieldset')
                            ->legend((new Legend(__('Social links (footer)'))))
                            ->fields([
                                (new Table())
                                    ->class('dragable')
                                    ->caption((new Caption(__('Social links (footer)')))->class('sr-only'))
                                    ->items([
                                        (new Thead())->items([
                                            (new Tr())->items([
                                                (new Th())->text(''),
                                                (new Th())->text(__('Image')),
                                                (new Th())->scope('row')->text(__('Label')),
                                                (new Th())->text(__('URL')),
                                            ]),
                                        ]),
                                        (new Tbody())->id('stickerslist')->items(
                                            array_map(function ($i, $v) {
                                                return (new Tr())
                                                    ->class('line')
                                                    ->id('l_' . $i)
                                                    ->items([
                                                        (new Td())->class('handle')->items([
                                                            (new Number('order[' . $i . ']', 0, count(App::backend()->stickers), $i + 1))
                                                                ->class('position'),
                                                            (new Hidden(['dynorder[]', 'dynorder-' . $i], (string) $i)),
                                                        ]),
                                                        (new Td())->class('linkimg')->items([
                                                            (new Hidden('sticker_image[]'))
                                                                ->value($v['image']),
                                                            (new Text('i'))
                                                                ->class($v['image'])
                                                                ->title($v['label']),
                                                        ]),
                                                        (new Td())->items([
                                                            (new Input(['sticker_label[]', 'dsl-' . $i]))
                                                                ->size(20)
                                                                ->maxlength(255)
                                                                ->value($v['label']),
                                                        ]),
                                                        (new Td())->items([
                                                            (new Input(['sticker_url[]', 'dsu-' . $i]))
                                                                ->size(40)
                                                                ->maxlength(255)
                                                                ->value($v['url']),
                                                        ]),
                                                    ]);
                                            }, array_keys(App::backend()->stickers), App::backend()->stickers)
                                        ),
                                    ]),
                            ]),
                        (new Para())->items([
                            (new Hidden('conf_tab'))
                                ->value('stickers'),
                        ]),
                        (new Para())->class('clear')->items([
                            (new Hidden('ds_order'))
                                ->value(''),
                            (new Submit(['stickers'], __('Save'))),
                            App::nonce()->formNonce(),
                        ]),
                    ]),
            ])
        ->render();

        App::backend()->page()->helpBlock('simplegrayscale');

        // Legacy mode
        if (!App::backend()->standalone_config) {
            echo '<form style="display:none">';
        }
    }
}
