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

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    "Simple Grayscale",                           		// Name
    "Simple Grayscale Bootstrap theme for Dotclear",  	// Description
    "Start Bootstrap and Philippe aka amalgame",        // Author
    '1.6.1',                                     		// Version
    [                                          			// Properties
        'requires'          => [['core', '2.16']], 		// Dependencies
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset' 			=> 'mustek'
    ]
);
