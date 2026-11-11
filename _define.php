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
if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'Simple Grayscale',
        'Simple Grayscale Bootstrap 5 theme for Dotclear',
        'Philippe aka amalgame and contributors',
        '4.11',
        [
            'date'              => '2026-11-11T00:00:08+0100',
            'requires'          => [['core', '2.40']],
            'standalone_config' => true,
            'type'              => 'theme',
            'tplset'            => 'mustek',
            'overload'          => true,
        ]
    );
}
