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

// Requirements
$gBitSystem->registerRequirements( ACCOUNTS_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.5', ),
	'libertygraph' => array( 'min' => '0.0.0', ),
));

$gBitSystem->registerPackageInfo( ACCOUNTS_PKG_NAME, array(
	'description' => "Estabilishes accounts for clustering users and managing access control",
	'license' => '<a href="http://www.gnu.org/copyleft/lesser.html">LGPL</a>',));


// Install process
global $gBitInstaller;
if( is_object( $gBitInstaller ) ){

$tables = array(
    'account_data' => "
		account_id I4 PRIMARY,
		content_id I4 NOTNULL 
        CONSTRAINT '
        , CONSTRAINT `account_content_ref` FOREIGN KEY (`content_id`) REFERENCES `liberty_content` (`content_id`)
		'
	",
);

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( ACCOUNTS_PKG_NAME, $tableName, $tables[$tableName] );
}

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ACCOUNTS_PKG_NAME, $indices );

// Sequences
$gBitInstaller->registerSchemaSequences( ACCOUNTS_PKG_NAME, array (
	'account_data_id_seq' => array( 'start' => 1 ),
));

// Schema defaults
$defaults = array(
);
if (count($defaults) > 0) {
	$gBitInstaller->registerSchemaDefault( ACCOUNTS_PKG_NAME, $defaults);
}


// User Permissions
$gBitInstaller->registerUserPermissions( ACCOUNTS_PKG_NAME, array(
	array ( 'p_accounts_admin'  , 'Can admin the accounts package', 'admin'      , ACCOUNTS_PKG_NAME ),
	array ( 'p_accounts_view'  , 'Can view the accounts package', 'admin'      , ACCOUNTS_PKG_NAME ),
	array ( 'p_account_create' , 'Can create a account entry'   , 'admin' , ACCOUNTS_PKG_NAME ),
	array ( 'p_account_view'   , 'Can view account entries'     , 'basic'      , ACCOUNTS_PKG_NAME ),
	array ( 'p_account_update' , 'Can update any account entry' , 'admin'    , ACCOUNTS_PKG_NAME ),
	array ( 'p_account_expunge', 'Can delete any account entry' , 'admin'      , ACCOUNTS_PKG_NAME ),
	array ( 'p_account_admin'  , 'Can admin any account entry'  , 'admin'      , ACCOUNTS_PKG_NAME ),
));

// Default Preferences
$gBitInstaller->registerPreferences( ACCOUNTS_PKG_NAME, array(
	array ( ACCOUNTS_PKG_NAME , 'account_default_ordering'      , 'account_id_desc' ),
	array ( ACCOUNTS_PKG_NAME , 'account_list_title'            , 'y'              ),
	array ( ACCOUNTS_PKG_NAME , 'accounts_account_home_id'               , 0                ),
	array ( ACCOUNTS_PKG_NAME , 'accounts_home_type'                    , 'bitaccount'      ),
));

// ### Register content types
$gBitInstaller->registerContentObjects( ACCOUNTS_PKG_NAME, array(
    'BitAccount'=>ACCOUNTS_PKG_PATH.'BitAccount.php',
));

// Process plugin settings
$gBitInstaller->loadPackagePluginSchemas( ACCOUNTS_PKG_NAME );

}
