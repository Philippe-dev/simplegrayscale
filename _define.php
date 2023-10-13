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
$this->registerModule(
    'Simple Grayscale',
    'Simple Grayscale Bootstrap 5 theme for Dotclear',
    'Philippe aka amalgame and contributors',
    '4.0',
    [
        'requires'          => [['core', '2.28']],
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset'            => 'mustek',
    ]
);
