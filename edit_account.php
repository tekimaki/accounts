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


// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'accounts' );

/* =-=- CUSTOM BEGIN: security -=-= */

// If $gAccount is set run with it
if( !empty( $gAccount ) && $gAccount->isValid() ){
	$_REQUEST['account_content_id'] = $gAccount->mContentId;
	// this will prevent a double content load
	$gContent = &$gAccount;
	// for now we call load again here - gAccount calls load in dns pluging but everything is not loeaded at that point
	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}

/* =-=- CUSTOM END: security -=-= */

require_once( ACCOUNTS_PKG_PATH.'lookup_account_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ){
	$gContent->verifyUpdatePermission();
}else{
	$gContent->verifyCreatePermission();
}

// Check if the page has changed
if( !empty( $_REQUEST["save_account"] ) ) {
	// Editing requires general ticket verification
	$gBitUser->verifyTicket();

	if( $gContent->store( $_REQUEST ) ) {
		if( $gBitSystem->getConfig('edit_success_return_to_form')=='n' ){
			bit_redirect( $gContent->getDisplayUrl() );
		}else{
			$gBitSmarty->assign_by_ref( 'success', tra( 'Your account has been updated' ) );
		}
	} else {
		// if store fails set preview
		$_REQUEST['preview'] = TRUE;
		// And put all the variables into the object
		$gContent->preparePreview( $_REQUEST );
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

// If we are in preview mode then preview it!
if( isset( $_REQUEST["preview"] ) ) {
	// Run verify so they see any errors with their preview
	$gContent->verify( $_REQUEST );
	// Put all the variables into the object
	$gContent->preparePreview( $_REQUEST );
	$gContent->invokeServices( 'content_preview_function', $_REQUEST );
	$gBitSmarty->assign( 'preview', TRUE );
} else {
	$gContent->invokeServices( 'content_edit_function', $_REQUEST );
}


// Prep any data we may need for the form


/* =-=- CUSTOM BEGIN: edit -=-= */

/* =-=- CUSTOM END: edit -=-= */

// Include any javascript files we need for editing



// Display the template
$gBitSystem->display( 'bitpackage:accounts/edit_account.tpl', tra('Edit Account') , array( 'display_mode' => 'edit' ));

