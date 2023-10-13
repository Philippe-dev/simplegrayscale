<?php
/**
 * @file
 * @brief 		The module backend helper resource
 * @ingroup 	simplegrayscale
 *
 * @package 	Dotclear
 *
 * @copyright 	Olivier Meunier & Association Dotclear
 * @copyright 	GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Theme\simplegrayscale;

use Dotclear\App;

App::backend()->resources->set('help', 'simplegrayscale', __DIR__ . '/help/help.html');
