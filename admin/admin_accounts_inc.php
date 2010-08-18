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
		'label' => 'Title',
		'note' => 'Display the title.',
	),
	"account_list_data" => array(
		'label' => 'About',
		'note' => 'Display the body text.',
	),
        "account_list_title" => array(
		'label' => 'Account Name',
		'note' => 'Display the title',
	),
        "account_list_press" => array(
		'label' => 'Press',
		'note' => 'Display the press',
	),
        "account_list_message" => array(
		'label' => 'Mayor/CSO Message',
		'note' => 'Display the message',
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

// Process plugin settings for each content type in the package
$Account = new BitAccount();
$Account->invokeService( 'content_admin_func', $_REQUEST );

