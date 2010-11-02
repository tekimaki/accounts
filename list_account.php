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


require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'accounts' );

/* =-=- CUSTOM BEGIN: security -=-= */

/* DEPRECATED jailed at DNS plugin - may still want to use this anyway, but dont need to right now
// If $gAccount is set run with it
if( is_object( $gAccount ) && $gAccount->isValid() ){
	$_REQUEST['account_content_id'] = $gAccount->mContentId;
	// this will prevent a double content load
	$gContent = &$gAccount;
	// for now we call load again here - gAccount calls load in dns pluging but everything is not loeaded at that point
	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
*/

/* =-=- CUSTOM END: security -=-= */

// Look up the content
require_once( ACCOUNTS_PKG_PATH.'lookup_account_inc.php' );

// Now check permissions to access this page
$gContent->verifyListViewPermission();

// Remove account data if we don't want them anymore
if( isset( $_REQUEST["submit_mult"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["submit_mult"] == "remove_account_data" ) {

	// Now check permissions to remove the selected accounts data
	$gContent->verifyUserPermission( 'p_account_expunge' );

	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['delete'] = TRUE;
		$formHash['submit_mult'] = 'remove_account_data';
		foreach( $_REQUEST["checked"] as $del ) {
			$tmpInst = new BitAccount($del);
			if ( $tmpInst->load() && !empty( $tmpInst->mInfo['title'] )) {
				$info = $tmpInst->mInfo['title'];
			} else {
				$info = $del;
			}
			$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>'.$info;
		}
		$gBitSystem->confirmDialog( $formHash,
			array(
				'label' => 'Remove '.$gContent->getContentTypeName( count( $_REQUEST["checked"] )>1 ),
				'warning' => tra('Are you sure you want to delete '.count( $_REQUEST["checked"] ).' '.$gContent->getContentTypeName( count( $_REQUEST["checked"] )>1  ).' records?'),
				'error' => tra('This cannot be undone!')
			)
		);
	} else {
		foreach( $_REQUEST["checked"] as $deleteId ) {
			$tmpInst = new BitAccount( $deleteId );
			if( !$tmpInst->load() || !$tmpInst->expunge() ) {
				array_merge( $errors, array_values( $tmpInst->mErrors ) );
			}
		}
		if( !empty( $errors ) ) {
			$gBitSmarty->assign_by_ref( 'errors', $errors );
		}
	}
}

// Create new BitAccount object
$obj = new BitAccount();
$list = $obj->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'accountList', $list );

// getList() has now placed all the pagination information in $_REQUEST['listInfo']
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );


/* =-=- CUSTOM BEGIN: list -=-= */
/* =-=- CUSTOM END: list -=-= */


// Display the template
$gBitSystem->display( 'bitpackage:accounts/list_account.tpl', tra( $gContent->getContentTypeName( TRUE ) ) , array( 'display_mode' => 'list' ));

