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


/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

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

include_once( ACCOUNTS_PKG_PATH.'lookup_account_inc.php' );

$gBitSystem->verifyPackage( 'accounts' );

if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( "No account indicated" );
}

$gContent->verifyExpungePermission();

if( isset( $_REQUEST["confirm"] ) ) {
	if( $gContent->expunge()  ) {
		header ("location: ".BIT_ROOT_URL );
		die;
	} else {
		$gBitSystem->fatalError( "Error while deleting: " + $gContent->mErrors );
	}
}

$gBitSystem->setBrowserTitle( tra( 'Confirm delete of: ' ).$gContent->getTitle() );
$formHash['remove'] = TRUE;
$formHash['account_id'] = $_REQUEST['account_id'];
$msgHash = array(
	'label' => tra( 'Delete Account' ),
	'confirm_item' => $gContent->getTitle(),
	'warning' => tra( 'This account will be completely deleted.<br />This cannot be undone!' ),
);
$gBitSystem->confirmDialog( $formHash,$msgHash );

