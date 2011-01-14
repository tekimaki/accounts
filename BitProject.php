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
* BitProject class
* A class which represents a project.
*
* @version $Revision: $
* @class BitProject
*/

/**
 * Initialize
 */
require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */

/* =-=- CUSTOM END: require -=-= */


/**
* This is used to uniquely identify the object
*/
define( 'BITPROJECT_CONTENT_TYPE_GUID', 'bitproject' );

class BitProject extends LibertyMime {
	/**
	 * mProjectId Primary key for our Project class object & table
	 *
	 * @var array
	 * @access public
	 */
	var $mProjectId;

	var $mVerification;

	var $mSchema;

	/**
	 * BitProject During initialisation, be sure to call our base constructors
	 *
	 * @param numeric $pProjectId
	 * @param numeric $pContentId
	 * @access public
	 * @return void
	 */
	function BitProject( $pProjectId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->mProjectId = $pProjectId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITPROJECT_CONTENT_TYPE_GUID;
		$this->registerContentType( BITPROJECT_CONTENT_TYPE_GUID, array(
			'content_type_guid'	  => BITPROJECT_CONTENT_TYPE_GUID,
			'content_name' => 'Project',
			'content_name_plural' => 'Projects',
			'handler_class'		  => 'BitProject',
			'handler_package'	  => 'accounts',
			'handler_file'		  => 'BitProject.php',
			'maintainer_url'	  => 'http://www.tekimaki.com'
		));
		// Permission setup
		$this->mCreateContentPerm  = 'p_project_create';
		$this->mViewContentPerm	   = 'p_project_view';
		$this->mListViewContentPerm	= 'p_project_list';
		$this->mUpdateContentPerm  = 'p_project_update';
		$this->mExpungeContentPerm = 'p_project_expunge';
		$this->mAdminContentPerm   = 'p_accounts_admin';
	}

	/**
	 * load Load the data from the database
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->verifyId( $this->mProjectId ) || $this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = $this->verifyId( $this->mProjectId ) ? 'project_id' : 'content_id';
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mProjectId ) ? $this->mProjectId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "
				SELECT project.*, lc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name,
				lch.`hits`,
				lf.`storage_path` as avatar,
				lfp.storage_path AS `primary_attachment_path`
				$selectSql
				FROM `".BIT_DB_PREFIX."project_data` project
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = project.`content_id` ) $joinSql
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lch.`content_id` = lc.`content_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` a ON (uue.`user_id` = a.`user_id` AND uue.`avatar_attachment_id`=a.`attachment_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON (lf.`file_id` = a.`foreign_id`)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = la.`foreign_id` )
				WHERE project.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = $result->fields['content_id'];
				$this->mProjectId = $result->fields['project_id'];

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
		if( isset( $pParamHash['project']["title"] ) ) {
			$this->mInfo["title"] = $pParamHash['project']["title"];
		}

		if( isset( $pParamHash['project']["summary"] ) ) {
			$this->mInfo["summary"] = $pParamHash['project']["summary"];
		}

		if( isset( $pParamHash['project']["format_guid"] ) ) {
			$this->mInfo['format_guid'] = $pParamHash['project']["format_guid"];
		}

		if( isset( $pParamHash['project']["edit"] ) ) {
			$this->mInfo["data"] = $pParamHash['project']["edit"];
			$this->mInfo['parsed_data'] = $this->parseData();
		}
	}

	/**
	 * store Any method named Store inherently implies data will be written to the database
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This is the ONLY method that should be called in order to store( create or update ) an project!
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
			&& LibertyMime::store( $pParamHash['project'] ) ) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."project_data";
			if( $this->mProjectId ) {
				if( !empty( $pParamHash['project_store'] ) ){
					$locId = array( "project_id" => $pParamHash['project']['project_id'] );
					$table = BIT_DB_PREFIX."project_data";
					$result = $this->mDb->associateUpdate( $table, $pParamHash['project_store'], $locId );
				}
			} else {
				$new = TRUE;
				$pParamHash['project_store']['content_id'] = $pParamHash['project']['content_id'];
				if( @$this->verifyId( $pParamHash['project_id'] ) ) {
					// if pParamHash['project']['project_id'] is set, some is requesting a particular project_id. Use with caution!
					$pParamHash['project_store']['project_id'] = $pParamHash['project']['project_id'];
				} else {
					$pParamHash['project_store']['project_id'] = $this->mDb->GenID( 'project_data_id_seq' );
				}
				$this->mProjectId = $pParamHash['project_store']['project_id'];

				$result = $this->mDb->associateInsert( $table, $pParamHash['project_store'] );
			}



			/* =-=- CUSTOM BEGIN: store -=-= */
			if ( !empty( $pParamHash['project_store']['is_default'] ) ) {
				$this->clearDefaults();
			}
			if ( $new ) {
				$this->createDefaultSubProject($pParamHash['project']['content_store'], $pParamHash['project_store']);
			}
			/* =-=- CUSTOM END: store -=-= */


			$this->mDb->CompleteTrans();
			$this->load();
		} else {
			$this->mErrors['store'] = tra('Failed to save this '.$this->getContentTypeName());
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
		// make sure we're all loaded up of we have a mProjectId
		if( $this->verifyId( $this->mProjectId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['project']['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['project']['content_type_guid'] ) ) {
			$pParamHash['project']['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['project']['content_id'] ) ) {
			$pParamHash['project']['project_store']['content_id'] = $pParamHash['project']['content_id'];
		}

		if( !empty( $pParamHash['project']['data'] ) ) {
			$pParamHash['project']['edit'] = $pParamHash['project']['data'];
		}

		// Use $pParamHash here since it handles validation right
		$this->validateFields($pParamHash);

		// collapse the hash that is passed to parent class so that service data is passed through properly - need to do so before verify service call below
		$hashCopy = $pParamHash;
		$pParamHash['project'] = array_merge( $hashCopy, $pParamHash['project'] );


		/* =-=- CUSTOM BEGIN: verify -=-= */

		/* =-=- CUSTOM END: verify -=-= */


		// if we have an error we get them all by checking parent classes for additional errors and the typeMaps if there are any
		if( count( $this->mErrors ) > 0 ){
			// check errors of base class so we get them all in one go
			LibertyMime::verify( $pParamHash['project'] );
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


			$query = "DELETE FROM `".BIT_DB_PREFIX."project_data` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
			}
			$this->mDb->CompleteTrans();
			// If deleting the default/home project record then unset this.
			if( $ret && $gBitSystem->getConfig( 'project_home_id' ) == $this->mProjectId ) {
				$gBitSystem->storeConfig( 'project_home_id', 0, PROJECT_PKG_NAME );
			}
		}
		return $ret;
	}




	/**
	 * isValid Make sure project is loaded and valid
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mProjectId ) && @BitBase::verifyId( $this->mContentId ));
	}

	/**
	 * getList This function generates a list of records from the liberty_content database for use in a list page
	 *
	 * @param array $pParamHash
	 * @access public
	 * @return array List of project data
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

		if (empty($pParamHash['include_defaults'])) {
			$whereSql .= " AND project.is_default = 0";
		}

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
			SELECT project.*, lc.`content_id`, lc.`title`, lc.`data` $selectSql, lc.`format_guid`, lc.`user_id`, lc.`modifier_user_id`,
				uu.`email`, uu.`login`, uu.`real_name`
			FROM `".BIT_DB_PREFIX."project_data` project
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = project.`content_id` ) $joinSql
				INNER JOIN `".BIT_DB_PREFIX."users_users`     uu ON uu.`user_id`     = lc.`user_id`
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$sort_mode_prefix.$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "
			SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX."project_data` project
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = project.`content_id` ) $joinSql
				INNER JOIN `".BIT_DB_PREFIX."users_users`     uu ON uu.`user_id`     = lc.`user_id`
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {

			if ( $gBitSystem->isFeatureActive( 'project_list_data' ) 
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
	 * getDisplayUrl Generates the URL to the project page
	 * 
	 * @access public
	 * @return string URL to the project page
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
					$ret = ACCOUNTS_PKG_URL.'project/'.$this->mProjectId;
				} else {
					$ret = ACCOUNTS_PKG_URL."index.php?project_id=".$this->mProjectId;
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

        /* =-=- CUSTOM END: getEditUrl -=-= */

        // Did the section or custom code block give us a URL?
        if ($ret == NULL) {
            if( @$this->isValid() ) {
                if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
                    $ret = ACCOUNTS_PKG_URL.'project/edit/'.$this->mProjectId;
                } else {
                    $ret = ACCOUNTS_PKG_URL."edit_project.php?project_id=".$this->mProjectId;
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
		$this->mVerification['project_data'],
			$pParamHash['project'],
			$this->mInfo);
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateFields(&$pParamHash) {
		$this->prepVerify();
		LibertyValidator::validate(
			$this->mVerification['project_data'],
			$pParamHash['project'],
			$this->mErrors, $pParamHash['project_store']);
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify() {
	 	/* Validation for liberty_content - modify base settings */
		if (empty($this->mVerification['liberty_content'])) {
			LibertyContent::prepVerify();
	 		/* Validation for liberty_content title */
			$this->mVerification['liberty_content']['string']['title'] = array_merge( $this->mVerification['liberty_content']['string']['title'], array(
				'name' => 'Project Name',
				'required' => '1'
			));
	 		/* Validation for liberty_content data */
			$this->mVerification['liberty_content']['string']['data'] = array_merge( $this->mVerification['liberty_content']['string']['data'], array(
				'name' => 'Description',
			));
		}

		if (empty($this->mVerification['project_data'])) {

	 		/* Validation for is_default */
			$this->mVerification['project_data']['boolean']['is_default'] = array(
				'name' => 'Is Default',
			);
	 		/* Validation for account_content_id */
			$this->mVerification['project_data']['reference']['account_content_id'] = array(
				'name' => 'Account Name',
				'table' => 'liberty_content',
				'column' => 'content_id',
				'required' => '1'
			);

		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	public function getSchema() {
		if (empty($this->mSchema['project_data'])) {

	 		/* Schema for title */
			$this->mSchema['project_data']['title'] = array(
				'name' => 'title',
				'type' => 'null',
				'label' => 'Project Name',
				'help' => '',
				'required' => '1'
			);
	 		/* Schema for data */
			$this->mSchema['project_data']['data'] = array(
				'name' => 'data',
				'type' => 'null',
				'label' => 'Description',
				'help' => 'A description of the project',
			);
	 		/* Schema for is_default */
			$this->mSchema['project_data']['is_default'] = array(
				'name' => 'is_default',
				'type' => 'boolean',
				'label' => 'Is Default',
				'help' => '',
			);
	 		/* Schema for account_content_id */
			$this->mSchema['project_data']['account_content_id'] = array(
				'name' => 'account_content_id',
				'type' => 'reference',
				'label' => 'Account Name',
				'help' => '',
				'table' => 'liberty_content',
				'column' => 'content_id',
				'required' => '1'
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
		return $gBitSystem->mDb->getOne( "SELECT project_id FROM `".BIT_DB_PREFIX."project_data` project LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (project.`content_id` = lc.`content_id`) WHERE project.`".$pKey."` = ?", $pValue );
	}
	
	// Getters for reference column options - return associative arrays formatted for generating html select inputs
	function getAccountNameOptions( &$pParamHash=array() ){
		$bindVars = array();
		$joinSql = $whereSql = "";
		/* =-=- CUSTOM BEGIN: account_content_id_options -=-= */

		/* =-=- CUSTOM END: account_content_id_options -=-= */
		$query = "SELECT a.content_id, b.title FROM account_data a INNER JOIN liberty_content b ON a.content_id = b.content_id $joinSql $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}





	// {{{ =================== Custom Helper Mthods  ====================


	/* This section is for any helper methods you wish to create */
	/* =-=- CUSTOM BEGIN: methods -=-= */

	/**
	 * Clear Defaults to make sure we are the only one marked as a default
	 **/
	function clearDefaults() {
		if ($this->isValid() && !empty($this->mInfo['account_id'])) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."project_data";
			$sql = "UPDATE `".$table."` SET is_default = 'n' WHERE account_id == ? AND project_id != ?";
			$location = array($this->mInfo['account_id'], $this->mProjectId);
			$this->mDb->query($sql, $location);
		}
	}

	/**
	 * Creates the default project for this account
	 */
	function createDefaultSubProject($pContentHash, $pParamHash) {
		require_once(ACCOUNTS_PKG_PATH.'BitSubProject.php');
		$bsp = new BitSubProject();
		$store = array();
		$store['subproject']['title'] = $pContentHash['title'];
		$store['subproject']['edit'] = 'Default SubProject For Project';
		$store['subproject']['account_content_id'] = $pParamHash['account_content_id'];
		$store['subproject']['project_content_id'] = $this->mContentId;
		$store['subproject']['is_default'] = 1;
		$bsp->store($store);

		if ($bsp->isValid()) {
			$this->storePreference('default_subproject_content_id', $bsp->mContentId);
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
