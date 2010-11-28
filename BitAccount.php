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

/**
 * Initialize
 */
require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */
require_once( USERS_PKG_PATH.'BitUser.php' );

define( 'BIACCOUNT_DRAFT_STATUS_ID', -5 );
define( 'BIACCOUNT_PROVISIONAL_STATUS_ID', 10 );
define( 'BIACCOUNT_PUBLIC_STATUS_ID', 50 );

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

	/**
	 * load Load the data from the database
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->verifyId( $this->mAccountId ) || $this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = $this->verifyId( $this->mAccountId ) ? 'account_id' : 'content_id';
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mAccountId ) ? $this->mAccountId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "
				SELECT account.*, lc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name,
				lch.`hits`,
				lf.`storage_path` as avatar,
				lfp.storage_path AS `primary_attachment_path`
				$selectSql
				FROM `".BIT_DB_PREFIX."account_data` account
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = account.`content_id` ) $joinSql
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lch.`content_id` = lc.`content_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uue.`user_id` = a.`user_id` AND uue.`avatar_attachment_id`=a.`attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = la.`foreign_id` )
				WHERE account.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = $result->fields['content_id'];
				$this->mAccountId = $result->fields['account_id'];

				$this->mInfo['creator'] = ( !empty( $result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] = ( !empty( $result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_name'] = BitUser::getTitle( $this->mInfo );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData();

				/* =-=- CUSTOM BEGIN: load -=-= */

				/* =-=- CUSTOM END: load -=-= */

				LibertyMime::load();
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	* Deal with text and images, modify them apprpriately that they can be returned to the form.
	* @param $pParamHash data submitted by form - generally $_REQUEST
	* @return array of data compatible with edit form
	* @access public
	**/
	function preparePreview( &$pParamHash ){
		global $gBitSystem, $gBitUser;

		if( empty( $this->mInfo['user_id'] ) ) {
			$this->mInfo['user_id'] = $gBitUser->mUserId;
			$this->mInfo['creator_user'] = $gBitUser->getField( 'login' );
			$this->mInfo['creator_real_name'] = $gBitUser->getField( 'real_name' );
		}

		$this->mInfo['creator_user_id'] = $this->mInfo['user_id'];

		if( empty( $this->mInfo['created'] ) ){
			$this->mInfo['created'] = $gBitSystem->getUTCTime();
		}

		$this->previewFields($pParamHash);


		// Liberty should really have a preview function that handles these
		// But it doesn't so we handle them here.
		if( isset( $pParamHash['account']["title"] ) ) {
			$this->mInfo["title"] = $pParamHash['account']["title"];
		}

		if( isset( $pParamHash['account']["summary"] ) ) {
			$this->mInfo["summary"] = $pParamHash['account']["summary"];
		}

		if( isset( $pParamHash['account']["format_guid"] ) ) {
			$this->mInfo['format_guid'] = $pParamHash['account']["format_guid"];
		}

		if( isset( $pParamHash['account']["edit"] ) ) {
			$this->mInfo["data"] = $pParamHash['account']["edit"];
			$this->mInfo['parsed_data'] = $this->parseData();
		}
	}

	/**
	 * store Any method named Store inherently implies data will be written to the database
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This is the ONLY method that should be called in order to store( create or update ) an account!
	 * It is very smart and will figure out what to do for you. It should be considered a black box.
	 *
	 * @param array $pParamHash hash of values that will be used to store the data
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function store( &$pParamHash ) {
		// Don't allow uses to cut off an abort in the middle.
		// This is particularly important for classes which will
		// touch the filesystem in some way.
		$abort = ignore_user_abort(FALSE);
		// A flag to let the custom store block know if we updated or inserted.
		$new = FALSE;
		if( $this->verify( $pParamHash )
			&& LibertyMime::store( $pParamHash['account'] ) ) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."account_data";
			if( $this->mAccountId ) {
				if( !empty( $pParamHash['account_store'] ) ){
					$locId = array( "account_id" => $pParamHash['account']['account_id'] );
					$table = BIT_DB_PREFIX."account_data";
					$result = $this->mDb->associateUpdate( $table, $pParamHash['account_store'], $locId );
				}
			} else {
				$new = TRUE;
				$pParamHash['account_store']['content_id'] = $pParamHash['account']['content_id'];
				if( @$this->verifyId( $pParamHash['account_id'] ) ) {
					// if pParamHash['account']['account_id'] is set, some is requesting a particular account_id. Use with caution!
					$pParamHash['account_store']['account_id'] = $pParamHash['account']['account_id'];
				} else {
					$pParamHash['account_store']['account_id'] = $this->mDb->GenID( 'account_data_id_seq' );
				}
				$this->mAccountId = $pParamHash['account_store']['account_id'];

				$result = $this->mDb->associateInsert( $table, $pParamHash['account_store'] );
			}



			/* =-=- CUSTOM BEGIN: store -=-= */
			if ($new) {
				$this->createDefaultProject($pParamHash['account']['content_store']);
			}
			/* =-=- CUSTOM END: store -=-= */


			$this->mDb->CompleteTrans();
			$this->load();
		} else {
			$this->mErrors['store'] = tra('Failed to save this').' account.';
		}
		// Restore previous state for user abort
		ignore_user_abort($abort);
		return( count( $this->mErrors )== 0 );
	}

	/**
	 * verify Make sure the data is safe to store
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	 * NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	 *
	 * @param array $pParamHash reference to hash of values that will be used to store the page, they will be modified where necessary
	 * @access private
	 * @return boolean TRUE on success, FALSE on failure - $this->mErrors will contain reason for failure
	 */
	function verify( &$pParamHash ) {
		// make sure we're all loaded up of we have a mAccountId
		if( $this->verifyId( $this->mAccountId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['account']['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['account']['content_type_guid'] ) ) {
			$pParamHash['account']['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['account']['content_id'] ) ) {
			$pParamHash['account']['account_store']['content_id'] = $pParamHash['account']['content_id'];
		}

		// Use $pParamHash here since it handles validation right
		$this->validateFields($pParamHash);

		if( !empty( $pParamHash['account']['data'] ) ) {
			$pParamHash['account']['edit'] = $pParamHash['account']['data'];
		}

		// If title specified truncate to make sure not too long
		// TODO: This shouldn't be required. LC should validate this.
		if( !empty( $pParamHash['account']['title'] ) ) {
			$pParamHash['account']['content_store']['title'] = substr( $pParamHash['account']['title'], 0, 160 );
		} else if( empty( $pParamHash['account']['title'] ) ) { // else is error as must have title
			$this->mErrors['title'] = tra('You must enter a title for this '.$this->getContentTypeName());
		}

		// collapse the hash that is passed to parent class so that service data is passed through properly - need to do so before verify service call below
		$hashCopy = $pParamHash;
		$pParamHash['account'] = array_merge( $hashCopy, $pParamHash['account'] );


		/* =-=- CUSTOM BEGIN: verify -=-= */

		/* =-=- CUSTOM END: verify -=-= */


		// if we have an error we get them all by checking parent classes for additional errors and the typeMaps if there are any
		if( count( $this->mErrors ) > 0 ){
			// check errors of base class so we get them all in one go
			LibertyMime::verify( $pParamHash['account'] );
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	 * expunge
	 *
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function expunge() {
		global $gBitSystem;
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();


			/* =-=- CUSTOM BEGIN: expunge -=-= */

			/* =-=- CUSTOM END: expunge -=-= */


			$query = "DELETE FROM `".BIT_DB_PREFIX."account_data` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
			}
			$this->mDb->CompleteTrans();
			// If deleting the default/home account record then unset this.
			if( $ret && $gBitSystem->getConfig( 'account_home_id' ) == $this->mAccountId ) {
				$gBitSystem->storeConfig( 'account_home_id', 0, ACCOUNT_PKG_NAME );
			}
		}
		return $ret;
	}




	/**
	 * isValid Make sure account is loaded and valid
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mAccountId ) && @BitBase::verifyId( $this->mContentId ));
	}

	/**
	 * getList This function generates a list of records from the liberty_content database for use in a list page
	 *
	 * @param array $pParamHash
	 * @access public
	 * @return array List of account data
	 */
	function getList( &$pParamHash ) {
		global $gBitSystem;
		// this makes sure parameters used later on are set
		LibertyContent::prepGetList( $pParamHash );

		$selectSql = $joinSql = $whereSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars, NULL, $pParamHash );


		/* =-=- CUSTOM BEGIN: getList -=-= */

		// @TODO this seems strange to limit to a list of one.
		/*
		if (!empty($pParamHash['account_content_id'])) {
			$whereSql .= " AND lc.`content_id` = ? ";
			$bindVars[] = $pParamHash['account_content_id'];
		}
		*/

		/* =-=- CUSTOM END: getList -=-= */


		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );

		if (empty($sort_mode) || ! strpos($sort_mode, '.') ) {
			$sort_mode_prefix = 'lc.';
		} else {
			$sort_mode_prefix = '';
		}

		if( is_array( $find ) ) {
			// you can use an array of pages
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0,count( $find ),'?' ) )." )";
			$bindVars = array_merge ( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` )like ? ";
			$bindVars[] = '%' . strtoupper( $find ). '%';
		}

		$query = "
			SELECT account.*, lc.`content_id`, lc.`title`, lc.`data` $selectSql, lc.`format_guid`, lc.`user_id`, lc.`modifier_user_id`,
				uu.`email`, uu.`login`, uu.`real_name`
			FROM `".BIT_DB_PREFIX."account_data` account
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = account.`content_id` ) $joinSql
				INNER JOIN `".BIT_DB_PREFIX."users_users`     uu ON uu.`user_id`     = lc.`user_id`
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$sort_mode_prefix.$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "
			SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX."account_data` account
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = account.`content_id` ) $joinSql
				INNER JOIN `".BIT_DB_PREFIX."users_users`     uu ON uu.`user_id`     = lc.`user_id`
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {

			if ( $gBitSystem->isFeatureActive( 'account_list_data' ) 
				|| !empty( $pParamHash['parse_data'] )
			){
				// parse data if to be displayed in lists 
				$parseHash['format_guid']	= $res['format_guid'];
				$parseHash['content_id']	= $res['content_id'];
				$parseHash['user_id']		= $res['user_id'];
				$parseHash['data']			= $res['data'];
				$res['parsed_data'] = $this->parseData( $parseHash ); 
			}

			/* =-=- CUSTOM BEGIN: getListIter -=-= */

			/* =-=- CUSTOM END: getListIter -=-= */

			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		// add all pagination info to pParamHash
		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}

	/**
	 * getDisplayUrl Generates the URL to the account page
	 * 
	 * @access public
	 * @return string URL to the account page
	 */
	function getDisplayUrl($pSection = NULL) {
		global $gBitSystem;
		$ret = NULL;

		/* =-=- CUSTOM BEGIN: getDisplayUrl -=-= */
		global $gAccount;
		if ($gAccount == $this) {
			$ret = '/';
		} else {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
				$ret = ACCOUNTS_PKG_URL.$this->mAccountId;
			} else {
				$ret = ACCOUNTS_PKG_URL."index.php?account_id=".$this->mAccountId;
			}
		}

		/* =-=- CUSTOM END: getDisplayUrl -=-= */		

		// Did the custom code block give us a URL?
		if ($ret == NULL) {
			if( @$this->isValid() ) {
				if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
					$ret = ACCOUNTS_PKG_URL.'account/'.$this->mAccountId;
				} else {
					$ret = ACCOUNTS_PKG_URL."index.php?account_id=".$this->mAccountId;
				}
			}
		}

		// Do we have a section request
		if (!empty($pSection)) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
				if ( substr($ret, -1, 1) != "/" ) {
					$ret .= "/";
				}
				$ret .= $pSection;
			} else {
				if (preg_match('|\?|', $ret)) {
					$ret .= '&';
				} else {
					$ret .= '?';
				}
				$ret .= "section=".$pSection;
			}
		}

		return $ret;
	}


    function getEditUrl($pSection = NULL){
        global $gBitSystem;
        $ret = NULL;

		// section edit url is the display url + /edit 
        if( !empty($pSection) ){
            $ret = $this->getDisplayUrl($pSection).'/edit';
        }

        /* =-=- CUSTOM BEGIN: getEditUrl -=-= */
		if ($ret == NULL) {
			global $gAccount;
			if ($gAccount == $this) {
				$ret = '/edit';
			} else {
				if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
					$ret = ACCOUNTS_PKG_URL.'edit/'.$this->mAccountId;
				} else {
					$ret = ACCOUNTS_PKG_URL."edit_account.php?account_id=".$this->mAccountId;
				}
			}
		}

        /* =-=- CUSTOM END: getEditUrl -=-= */

        // Did the section or custom code block give us a URL?
        if ($ret == NULL) {
            if( @$this->isValid() ) {
                if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
                    $ret = ACCOUNTS_PKG_URL.'account/edit/'.$this->mAccountId;
                } else {
                    $ret = ACCOUNTS_PKG_URL."edit_account.php?account_id=".$this->mAccountId;
                }
            }
        }

        return $ret;
    }


	/**
	 * previewFields prepares the fields in this type for preview
	 */
	function previewFields(&$pParamHash) {
		$this->prepVerify();
		LibertyValidator::preview(
		$this->mVerification['account_data'],
			$pParamHash['account'],
			$this->mInfo);
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateFields(&$pParamHash) {
		$this->prepVerify();
		LibertyValidator::validate(
			$this->mVerification['account_data'],
			$pParamHash['account'],
			$this, $pParamHash['account_store']);
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify() {
		if (empty($this->mVerification['account_data'])) {


		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	public function getSchema() {
		if (empty($this->mSchema['account_data'])) {

	 		/* Schema for title */
			$this->mSchema['account_data']['title'] = array(
				'name' => 'title',
				'type' => 'null',
				'label' => 'Account Name',
				'help' => '',
			);
	 		/* Schema for data */
			$this->mSchema['account_data']['data'] = array(
				'name' => 'data',
				'type' => 'null',
				'label' => 'About',
				'help' => 'A statement about the account.',
			);
		}


		return $this->mSchema;
	}
	
	/**
	 * getIdByField
	 * get id by type fields
	 */
	public static function getIdByField( $pKey, $pValue ) {
		global $gBitSystem;
		return $gBitSystem->mDb->getOne( "SELECT account_id FROM `".BIT_DB_PREFIX."account_data` account LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (account.`content_id` = lc.`content_id`) WHERE account.`".$pKey."` = ?", $pValue );
	}
	
	// Getters for reference column options - return associative arrays formatted for generating html select inputs

	/**
	 * getAvailableContentStatuses
	 */
	function getAvailableContentStatuses( $pUserMinimum=-6, $pUserMaximum=51 ) {
		global $gBitUser;
		// standard list of options
		if( !$gBitUser->hasPermission( 'p_liberty_edit_all_status' )) {
			$ret = array( 
				BIACCOUNT_DRAFT_STATUS_ID => "Draft",
				BIACCOUNT_PROVISIONAL_STATUS_ID => "Provisional",
				BIACCOUNT_PUBLIC_STATUS_ID => "Available",
			);
		}
		// for admins modify the master list of options
		else{
			$ret = LibertyMime::getAvailableContentStatuses( $pUserMinimum, $pUserMaximum );
			$ret[BIACCOUNT_DRAFT_STATUS_ID] = "Draft";
			$ret[BIACCOUNT_PROVISIONAL_STATUS_ID] = "Provisional";
			$ret[BIACCOUNT_PUBLIC_STATUS_ID] = "Available";
			ksort( $ret );
		}
        return $ret;
	}


	// {{{ =================== Custom Helper Mthods  ====================


	/* This section is for any helper methods you wish to create */
	/* =-=- CUSTOM BEGIN: methods -=-= */

	/**
	 * Creates the default project for this account
	 */
	function createDefaultProject($pParamHash) {
		require_once(ACCOUNTS_PKG_PATH.'BitProject.php');
		$bp = new BitProject();
		$store = array();
		$store['project']['title'] = $pParamHash['title'];
		$store['project']['edit'] = 'Default Project For Account';
		$store['project']['account_content_id'] = $this->mContentId;
		$store['project']['is_default'] = 1;
		$bp->store($store);
		// Store defaults in content preferences for easy access.
		if ($bp->isValid()) {
			$this->storePreference('default_project_content_id', $bp->mContentId);
			$this->storePreference('default_subproject_content_id', $bp->getPreference('default_subproject_content_id'));
		}
		// Copy any store errors;
		if( is_array( $bp->mErrors ) ){
			$this->mErrors = array_merge($this->mErrors, $bp->mErrors);		
		}
	}

	/**
	 * @TODO may want to support a list permission more broadly
	 */
	function verifyListViewPermission() {
		global $gBitSystem;
		if( $this->hasUserPermission( 'p_account_list' ) ){
			return TRUE;
		} else {
			$gBitSystem->setHttpStatus( 404 );
			$gBitSystem->fatalError(tra('The page you requested could not be found'));
		}
	}

	/**
	 * override parent permission verification so we can terminate with 404
	 */
	function verifyViewPermission(){
		global $gBitSystem;
		if( $this->hasViewPermission() ) {
			return TRUE;
		} else {
			$gBitSystem->setHttpStatus( 404 );
			$gBitSystem->fatalError(tra('The account you requested could not be found'));
		}
	}	

	/**
	 * override parent permission verification so we can terminate with 404
	 */
	function verifyUpdatePermission(){
		global $gBitSystem;
		if( $this->hasUpdatePermission() ) {
			return TRUE;
		} else {
			$gBitSystem->setHttpStatus( 404 );
			$gBitSystem->fatalError(tra('The account you requested could not be found'));
		}
	}	

	/* =-=- CUSTOM END: methods -=-= */


	// }}} -- end of Custom Helper Methods

}
