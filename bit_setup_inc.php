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

define( 'LIBERTY_SERVICE_ACCOUNT_SECURITY', 'account_security' );
define( 'LIBERTY_SERVICE_SUBPROJECT_CONTENT', 'subproject_content' );

global $gBitSystem;

$registerHash = array(
	'package_name' => 'accounts',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active and the user has view auth then register the package menu
if( $gBitSystem->isPackageActive( 'accounts' ) ){ //&& $gBitUser->hasPermission( 'p_accounts_view' ) ) {
	$menuHash = array(
		'package_name'  => ACCOUNTS_PKG_NAME,
		'index_url'     => ACCOUNTS_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:accounts/menu_accounts.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );

    // include service functions
	require_once( ACCOUNTS_PKG_PATH.'AccountSecurity.php' );

	/*
    $gLibertySystem->registerService(
		LIBERTY_SERVICE_ACCOUNT_SECURITY,
		ACCOUNTS_PKG_NAME,
        array(
			'content_user_perms_function' => 'account_security_content_user_perms',
        ),
        array(
			'description' => 'Provides account related security'
        )
    );
	*/
	require_once( ACCOUNTS_PKG_PATH.'SubProjectContent.php' );

	/*
    $gLibertySystem->registerService(
		LIBERTY_SERVICE_SUBPROJECT_CONTENT,
		ACCOUNTS_PKG_NAME,
        array(
			'content_edit_function' => 'subproject_content_content_edit',
			'content_store_function' => 'subproject_content_content_store',
			'content_expunge_function' => 'subproject_content_content_expunge',
			'content_display_function' => 'subproject_content_content_display',
			'content_preview_function' => 'subproject_content_content_preview',
        ),
        array(
			'description' => 'Stores content within a subproject'
        )
    );
	*/

$gLibertySystem->loadPackagePlugins( ACCOUNTS_PKG_NAME );

}
