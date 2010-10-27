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


global $gContent;
require_once( ACCOUNTS_PKG_PATH.'BitAccount.php');
//require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {

	// if account_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['account_id'] ) ) {
		$gContent = new BitAccount( $_REQUEST['account_id'] );

	// if content_id supplied, use that
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gContent = new BitAccount( NULL, $_REQUEST['content_id'] );

	} elseif (@BitBase::verifyId( $_REQUEST['account']['account_id'] ) ) {
		$gContent = new BitAccount( $_REQUEST['account']['account_id'] );

	// otherwise create new object
	} else {
/* =-=- CUSTOM BEGIN: create -=-= */
		$gContent = new BitAccount();
/* =-=- CUSTOM END: create -=-= */
	}

	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
