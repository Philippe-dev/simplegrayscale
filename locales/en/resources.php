<?php
/**
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

if (!isset(dcCore::app()->resources['help']['simplegrayscale'])) {
    dcCore::app()->resources['help']['simplegrayscale'] = dirname(__FILE__) . '/help/help.html';
}
