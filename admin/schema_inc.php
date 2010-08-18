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


$tables = array(
    'account_data' => "
		account_id I4 PRIMARY,
		content_id I4 NOTNULL, 
        press XL,
        message XL
        CONSTRAINT '
        , CONSTRAINT `account_content_ref` FOREIGN KEY (`content_id`) REFERENCES `liberty_content` (`content_id`)
		'
	",
    'account_account_theme' => "
        account_theme_id I4 PRIMARY,
        content_id I4 NOTNULL,
        primary_color C(6),
        secondard_color C(6),
        text_color C(6)
        CONSTRAINT '
        , CONSTRAINT `account_theme_content_ref` FOREIGN KEY (`content_id`) REFERENCES `liberty_content` (`content_id`)
        , UNIQUE(content_id)
        '
    ",
    'account_account_contact' => "
        account_contact_id I4 PRIMARY,
        content_id I4 NOTNULL,
        mayor_name C(160),
        cso_name C(160),
        street1 C(160),
        street2 C(160),
        city C(160),
        state C(2),
        pub_email C(200)
        CONSTRAINT '
        , CONSTRAINT `account_contact_content_ref` FOREIGN KEY (`content_id`) REFERENCES `liberty_content` (`content_id`)
        , UNIQUE(content_id)
        '
    ",
    'account_account_legal' => "
        account_legal_id I4 PRIMARY,
        content_id I4 NOTNULL,
        tos XL,
        privacy_policy XL
        CONSTRAINT '
        , CONSTRAINT `account_legal_content_ref` FOREIGN KEY (`content_id`) REFERENCES `liberty_content` (`content_id`)
        , UNIQUE(content_id)
        '
    ",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( ACCOUNTS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( ACCOUNTS_PKG_NAME, array(
	'description' => "Estabilishes accounts for clustering users and managing access control",
	'license' => '<a href="http://www.gnu.org/copyleft/lesser.html">LGPL</a>',));

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ACCOUNTS_PKG_NAME, $indices );

// Sequences
$gBitInstaller->registerSchemaSequences( ACCOUNTS_PKG_NAME, array (
	'account_data_id_seq' => array( 'start' => 1 ),
	'account_account_theme_id_seq' => array( 'start' => 1 ),
	'account_account_contact_id_seq' => array( 'start' => 1 ),
	'account_account_legal_id_seq' => array( 'start' => 1 ),
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
));

// Requirements
$gBitInstaller->registerRequirements( ACCOUNTS_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.5', ),
	'libertygraph' => array( 'min' => '0.0.0', ),

));

// Process plugin settings
$gBitInstaller->invokeService( ACCOUNTS_PKG_NAME, 'content_schema_inc_func' );

