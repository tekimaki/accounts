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

// Define content lookup keys
$typeNames = array(
		"account_name"	);
$typeIds = array(
		"account_id"	);
$typeContentIds = array(
		"account_content_id"	);

// If a content type key id is requested load it up
$requestType = NULL;
$requestKeyType = NULL;
foreach( $_REQUEST as $key => $val ) {
	if (in_array($key, $typeNames)) {
		$requestType = substr($key, 0, -5);
		$requestKeyType = 'name';
		break;
	}
	elseif (in_array($key, $typeIds)) {
		$requestType = substr($key, 0, -3);
		$requestKeyType = 'id';
		break;
	}
	elseif (in_array($key, $typeContentIds)) {
		$requestType = substr($key, 0, -10);
		$requestKeyType = 'content_id';
		break;
	}
}

// If there is an id to get, specified or default, then attempt to get it and display
if( !empty( $_REQUEST[$requestType.'name'] ) ||  
	!empty( $_REQUEST[$requestType.'_id'] ) || 
	!empty( $_REQUEST[$requestType.'_content_id'] ) ) {
	// Look up the content
	require_once( ACCOUNTS_PKG_PATH.'lookup_'.$requestType.'_inc.php' );

	if( !$gContent->isValid() ) {
		// Check permissions to access this content in general
		$gContent->verifyViewPermission();

		// They are allowed to see that this does not exist.
		$gBitSystem->setHttpStatus( 404 );
		$gBitSystem->fatalError( tra( "The requested ".$gContent->getContentTypeName()." (".$requestKeyType."=".$_REQUEST[$requestType.'_'.$requestKeyType].") could not be found." ) );
	}

	// Now check permissions to access this content
	$gContent->verifyViewPermission();

	// If package plugin page is specified invoke it - it is responsible for displaying the page
	if( !empty( $_REQUEST['page'] ) ){

		$gLibertySystem->invokeService( 'content_page_'.$_REQUEST['page'].'_func', $_REQUEST );

		// Display the plugin template
		$gBitSystem->display( 'bitpackage:accounts/plugins/templates/content_page_'.$_REQUEST['page'].'.tpl', htmlentities($gContent->getField('title', 'Accounts '.ucfirst($_REQUEST['page']))) , array( 'display_mode' => 'display' ));

	// Display the default content page
	}else{

		// Call display services
		$displayHash = array( 'perm_name' => $gContent->mViewContentPerm );
		$gContent->invokeServices( 'content_display_function', $displayHash );

		// Add a hit to the counter
		$gContent->addHit();

		/* =-=- CUSTOM BEGIN: indexload -=-= */
		/* =-=- CUSTOM END: indexload -=-= */

		// Display the template
		$gBitSystem->display( 'bitpackage:accounts/display_'.$requestType.'.tpl', htmlentities($gContent->getField('title', 'Accounts '.ucfirst($requestType))) , array( 'display_mode' => 'display' ));
	}

}else{

	/* =-=- CUSTOM BEGIN: index -=-= */
		$indexTitle = tra('Accounts');
		$gBitSmarty->assign( 'indexTitle', $indexTitle );
		$gBitSystem->display( 'bitpackage:accounts/display_index.tpl', $indexTitle, array( 'display_mode' => 'display' ));
	/* =-=- CUSTOM END: index -=-= */

}
