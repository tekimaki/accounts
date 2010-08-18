<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/* vim: :set fdm=marker : */
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
* BitAccount class
* A class which represents an account.
*
* @version $Revision: $
* @class BitAccount
*/

require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */

/* =-=- CUSTOM END: require -=-= */


/**
* This is used to uniquely identify the object
*/
define( 'BITACCOUNT_CONTENT_TYPE_GUID', 'bitaccount' );

class BitAccount extends LibertyMime {
	/**
	 * mAccountId Primary key for our Account class object & table
	 *
	 * @var array
	 * @access public
	 */
	var $mAccountId;

	var $mVerification;

	var $mSchema;

	/**
	 * BitAccount During initialisation, be sure to call our base constructors
	 *
	 * @param numeric $pAccountId
	 * @param numeric $pContentId
	 * @access public
	 * @return void
	 */
	function BitAccount( $pAccountId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->mAccountId = $pAccountId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITACCOUNT_CONTENT_TYPE_GUID;
		$this->registerContentType( BITACCOUNT_CONTENT_TYPE_GUID, array(
			'content_type_guid'	  => BITACCOUNT_CONTENT_TYPE_GUID,
			'content_name' => 'Account',
			'content_name_plural' => 'Accounts',
			'handler_class'		  => 'BitAccount',
			'handler_package'	  => 'accounts',
			'handler_file'		  => 'BitAccount.php',
			'maintainer_url'	  => 'http://www.tekimaki.com'
		));
		// Permission setup
		$this->mCreateContentPerm  = 'p_account_create';
		$this->mViewContentPerm	   = 'p_account_view';
		$this->mUpdateContentPerm  = 'p_account_update';
		$this->mExpungeContentPerm = 'p_account_expunge';
		$this->mAdminContentPerm   = 'p_accounts_admin';
	}

}
