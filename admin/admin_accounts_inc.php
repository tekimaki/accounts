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



$formRoles = array(
	"accounts_account_member_role" => array(
		'label' => 'Account Member Role',
		'note' => 'The group_id for the sub-project membership role.',
		'type' => 'int',
			),
	"accounts_project_member_role" => array(
		'label' => 'Project Member Role',
		'note' => 'The group_id for the project membership role.',
		'type' => 'int',
			),
);
$gBitSmarty->assign( 'formRoles', $formRoles );


require_once( ACCOUNTS_PKG_PATH.'BitAccount.php' );

$formaccountLists = array(
	"accounts_list_account_id" => array(
		'label' => 'Id',
		'note' => 'Display the account id.',
	),
	"account_list_title" => array(
		'label' => 'Account Name',
		'note' => 'Display the account name.',
	),
	"account_list_data" => array(
		'label' => 'About',
		'note' => 'Display the about text.',
	),
);
$gBitSmarty->assign( 'formaccountLists', $formaccountLists );


require_once( ACCOUNTS_PKG_PATH.'BitProject.php' );

$formprojectLists = array(
	"accounts_list_project_id" => array(
		'label' => 'Id',
		'note' => 'Display the project id.',
	),
	"project_list_title" => array(
		'label' => 'Project Name',
		'note' => 'Display the project name.',
	),
	"project_list_data" => array(
		'label' => 'Description',
		'note' => 'Display the description text.',
	),
        "project_list_is_default" => array(
		'label' => 'Is Default',
		'note' => 'Display the is_default',
	),
        "project_list_account_content_id" => array(
		'label' => 'Account Name',
		'note' => 'Display the account_content_id',
	),
);
$gBitSmarty->assign( 'formprojectLists', $formprojectLists );


require_once( ACCOUNTS_PKG_PATH.'BitSubProject.php' );

$formsubprojectLists = array(
	"accounts_list_subproject_id" => array(
		'label' => 'Id',
		'note' => 'Display the subproject id.',
	),
	"subproject_list_title" => array(
		'label' => 'Sub-Project Name',
		'note' => 'Display the sub-project name.',
	),
	"subproject_list_data" => array(
		'label' => 'Description',
		'note' => 'Display the description text.',
	),
        "subproject_list_account_content_id" => array(
		'label' => 'Account Name',
		'note' => 'Display the account_content_id',
	),
        "subproject_list_project_content_id" => array(
		'label' => 'Project Name',
		'note' => 'Display the project_content_id',
	),
        "subproject_list_is_default" => array(
		'label' => 'Is Default',
		'note' => 'Display the is_default',
	),
);
$gBitSmarty->assign( 'formsubprojectLists', $formsubprojectLists );





// Process the form if we've made some changes
if( !empty( $_REQUEST['accounts_settings'] ) ){

	simple_set_configs(  $formRoles, ACCOUNTS_PKG_NAME );


	$accountsToggles = array_merge( 
		$formaccountLists,		$formprojectLists,		$formsubprojectLists	);
	foreach( $accountsToggles as $item => $data ) {
		simple_set_toggle( $item, ACCOUNTS_PKG_NAME );
	}
	simple_set_int( 'accounts_account_home_id', ACCOUNTS_PKG_NAME );
	simple_set_int( 'accounts_project_home_id', ACCOUNTS_PKG_NAME );
	simple_set_int( 'accounts_subproject_home_id', ACCOUNTS_PKG_NAME );
	simple_set_value( 'accounts_home_type', ACCOUNTS_PKG_NAME );
	simple_set_value( 'accounts_home_format', ACCOUNTS_PKG_NAME );
}




// We require all records for home selection menu
// TODO: These should be selected with ajax magic instead
$_REQUEST['max_records'] = 0;


$obj = new BitAccount();
$obj_data = $obj->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'account_data', $obj_data);

$gBitSmarty->assign( 'homeFormatOptions', array( 'list'=>tra('List Content'), 'item'=>tra('Content Item') ));

$gBitSmarty->assign( 'accounts_account_home_id', 
	$gBitSystem->getConfig( "accounts_account_home_id" ));


$obj = new BitProject();
$obj_data = $obj->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'project_data', $obj_data);

$gBitSmarty->assign( 'homeFormatOptions', array( 'list'=>tra('List Content'), 'item'=>tra('Content Item') ));

$gBitSmarty->assign( 'accounts_project_home_id', 
	$gBitSystem->getConfig( "accounts_project_home_id" ));


$obj = new BitSubProject();
$obj_data = $obj->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'subproject_data', $obj_data);

$gBitSmarty->assign( 'homeFormatOptions', array( 'list'=>tra('List Content'), 'item'=>tra('Content Item') ));

$gBitSmarty->assign( 'accounts_subproject_home_id', 
	$gBitSystem->getConfig( "accounts_subproject_home_id" ));


$gBitSmarty->assign( 'accounts_home_format', 
	$gBitSystem->getConfig( "accounts_home_format" ));
$gBitSmarty->assign( 'accounts_home_type', 
	$gBitSystem->getConfig( "accounts_home_type" ));
$gBitSmarty->assign( 'homeTypes', array(
		'account',		'project',		'subproject'	));

// invoke content admin services
global $gLibertySystem;
$gLibertySystem->invokePackageServices( ACCOUNTS_PKG_NAME, 'package_admin_function', $_REQUEST );
