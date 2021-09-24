<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			      (c) 2017

WPML Configuration Module Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Wpml') && !class_exists('Plethora_Module_Wpml_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-framework/features/module/wpml/module-wpml.php
	*/
	class Plethora_Module_Wpml_Ext extends Plethora_Module_Wpml {
		
		// Set this to true, if you want to update wpml-config.xml file
		public $dev_mode = false;

	}
}