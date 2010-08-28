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

if (empty($requestType)) {
	// Use the home type and home content
	$requestType = $gBitSystem->getConfig("accounts_home_type", "account");
    if( $gBitSystem->getConfig( 'accounts_home_format', 'list' ) == 'list' ){
        include_once( ACCOUNTS_PKG_PATH.'list_'.$requestType.'.php' );
        die;
    }else{
		$_REQUEST[$requestType.'_id'] = $gBitSystem->getConfig( "accounts_".$requestType."_home_id" );
	}
}

// If there is an id to get, specified or default, then attempt to get it and display
if( !empty( $_REQUEST[$requestType.'_name'] ) ||
    !empty( $_REQUEST[$requestType.'_id'] ) ||
    !empty( $_REQUEST[$requestType.'_content_id'] ) ) {
	// Look up the content
	require_once( ACCOUNTS_PKG_PATH.'lookup_'.$requestType.'_inc.php' );

	if( !$gContent->isValid() ) {
		// Check permissions to access this content in general
		$gContent->verifyViewPermission();

		// They are allowed to see that this does not exist.
		$gBitSystem->setHttpStatus( 404 );
		$gBitSystem->fatalError( tra( "The requested ".$gContent->getContentTypeName()." (id=".$_REQUEST[$requestType.'_id'].") could not be found." ) );
	}

	// Now check permissions to access this content
	$gContent->verifyViewPermission();

		// If package plugin section is specified invoke the related service - it is responsible for displaying the section
	if( !empty( $_REQUEST['section'] ) ){
		// Someone is trying an attack - piss off
		if (preg_match("/[a-z_]/", $_REQUEST['section']) != 1) { 
			$gBitSystem->fatalError( tra('nice try') );
		}else{
			$sectionHash = $_REQUEST;
			$gContent->invokeServices( 'content_section_function', $sectionHash );
			if( empty( $sectionHash['has_section'] ) ){
				$gBitSystem->fatalError( tra('unknown section' ) );
			}

			// Display the plugin template
			$gBitSystem->display( 'bitpackage:liberty/service_content_display_section.tpl', htmlentities($gContent->getField('title', 'Accounts '.ucfirst($_REQUEST['section']))) , array( 'display_mode' => 'display' ));
			die;
		}
	}
	
	// Call display services
	$displayHash = array( 'perm_name' => $gContent->mViewContentPerm );
	$gContent->invokeServices( 'content_display_function', $displayHash );

	// Add a hit to the counter
	$gContent->addHit();

	/* =-=- CUSTOM BEGIN: indexload -=-= */
	
	/* =-=- CUSTOM END: indexload -=-= */

	// Display the template
	$gBitSystem->display( 'bitpackage:accounts/display_'.$requestType.'.tpl', htmlentities($gContent->getField('title', 'Accounts '.ucfirst($requestType))) , array( 'display_mode' => 'display' ));

} else if ( $gBitUser->hasPermission( 'p_accounts_admin' ) ) {
    // Redirect to set up the default accounts data to display
	header( "Location: ".KERNEL_PKG_URL.'admin/index.php?page='.ACCOUNTS_PKG_NAME );

} else {
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( tra( "The default accounts page has not been configured." ) );
}
