<?php

require( dirname( __FILE__ ).'/smarty/libs/Smarty.class.php' );

$smarty = new Smarty();

$smarty->template_dir 	= dirname( __FILE__ ).'/smarty/templates';
$smarty->compile_dir  	= dirname( __FILE__ ).'/smarty/templates_c';
$smarty->cache_dir 		= dirname( __FILE__ ).'/smarty/cache';
$smarty->config_dir 	= dirname( __FILE__ ).'/smarty/configs';

?>