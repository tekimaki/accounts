<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/**
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 * Copyright (c) 2010 Will James will@tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details *
 * $Id: $
 * @package accounts
 * @subpackage class
 */

/*
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
   Portions of this file are modifiable

   Anything between the CUSTOM BEGIN: and CUSTOM END:
   comments will be preserved on regeneration of this
   file.
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
*/


global $gBitSystem;

$registerHash = array(
	'package_name' => 'accounts',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active and the user has view auth then register the package menu
if( $gBitSystem->isPackageActive( 'accounts' ) && $gBitUser->hasPermission( 'p_accounts_view' ) ) {
	$menuHash = array(
		'package_name'  => ACCOUNTS_PKG_NAME,
		'index_url'     => ACCOUNTS_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:accounts/menu_accounts.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );


$gLibertySystem->loadPackagePlugins( ACCOUNTS_PKG_NAME );

}
