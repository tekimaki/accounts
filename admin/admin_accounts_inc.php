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





// Process the form if we've made some changes
if( !empty( $_REQUEST['accounts_settings'] ) ){



	$accountsToggles = array_merge( 
		$formaccountLists	);
	foreach( $accountsToggles as $item => $data ) {
		simple_set_toggle( $item, ACCOUNTS_PKG_NAME );
	}
}





// invoke content admin services
$BitAccount = new BitAccount();
$BitAccount->invokeServices( 'content_admin_function', $_REQUEST );
