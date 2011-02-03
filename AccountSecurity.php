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
* AccountSecurity class
* Provides account related security
*
* @version $Revision: $
* @class AccountSecurity
*/

/**
 * Initialize
 */
require_once( LIBERTY_PKG_PATH.'LibertyBase.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */

/* =-=- CUSTOM END: require -=-= */

class AccountSecurity extends LibertyBase {

	/**
	 * Primary key for parent object when instantiated
	 */
	var $mContentId;

	var $mVerification;

	var $mSchema;

	var $mServiceContent;

	public function __construct( $pContentId=NULL ) {
		LibertyBase::LibertyBase();
		$this->mContentId = $pContentId;
	}


	/**
	 * stores a single record in the account_security table
	 */
	function store( &$pParamHash ){
		if( $this->verify( $pParamHash ) ) {
			if ( !empty( $pParamHash['account_security_store'] ) ){
				$table = 'account_security_data';
				$this->mDb->StartTrans();
				if ( empty($pParamHash['account_security_store']['content_id']) && !empty( $this->mContentId ) ) {
					$pParamHash['account_security_store']['content_id'] = $this->mContentId;
				}
				if ( !empty( $pParamHash['account_security_store']['content_id'] ) && 
					 !$this->mDb->getOne( "SELECT * from ".$table." WHERE `content_id` = ? AND `user_id` = ? AND `group_id` = ?", array(
						$pParamHash['account_security_store']['content_id'], 
						$pParamHash['account_security_store']['user_id'], 
						$pParamHash['account_security_store']['group_id'] ) )
				){
					$result = $this->mDb->associateInsert( $table, $pParamHash['account_security_store'] );
				}
			}

			/* =-=- CUSTOM BEGIN: store -=-= */

			/* =-=- CUSTOM END: store -=-= */

			$this->mDb->CompleteTrans();
		}

		return (count($this->mErrors) == 0);
	}


	/** 
	 * verifies a data set for storage in the Account_security table
	 * data is put into $pParamHash['account_security_store'] for storage
	 */
	function verify( &$pParamHash ){
		// Use $pParamHash here since it handles validation right
		$this->validateFields($pParamHash);

		/* =-=- CUSTOM BEGIN: verify -=-= */

		/* =-=- CUSTOM END: verify -=-= */

		return( count( $this->mErrors )== 0 );
	}

	function expunge( &$pParamHash = array() ){
		$ret = FALSE;
		$this->mDb->StartTrans();
		$bindVars = array();
		$whereSql = "";

		// limit results by content_id
		if( !empty( $pParamHash['content_id'] ) ){
			$bindVars[] = $pParamHash['content_id'];
			$whereSql .= " WHERE `content_id` = ?";
		}

		/* =-=- CUSTOM BEGIN: expunge -=-= */
		if( empty( $pParamHash['content_id'] ) && is_object( $this->mServiceContent ) && $this->mServiceContent->mContentTypeGuid != BITUSER_CONTENT_TYPE_GUID ){
			$bindVars[] = $this->mServiceContent->mContentId;
			$whereSql .= " WHERE `content_id` = ?";
		}

		if( !empty( $pParamHash['group_id'] ) ){
			$bindVars[] = $pParamHash['group_id'];
			$whereSql .= "WHERE `group_id` = ?";
		}

		if( !empty( $pParamHash['user_id'] ) ){
			$bindVars[] = $pParamHash['user_id'];
			$whereSql .= "WHERE `user_id` = ?";
		}

		if( empty( $pParamHash['content_id'] ) && is_object( $this->mServiceContent ) && $this->mServiceContent->mContentTypeGuid == BITUSER_CONTENT_TYPE_GUID ){
			$bindVars[] = $this->mServiceContent->mUserId;
			$whereSql .= " WHERE `user_id` = ?";
		}

		/* =-=- CUSTOM END: expunge -=-= */
 
        // some sort of limit must be imposed to execute the expunge - nuking the whole table shall not be allowed
        if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );

			$query = "DELETE FROM `account_security_data` ".$whereSql;

			if( $this->mDb->query( $query, $bindVars ) ){
				$ret = TRUE;
			}
		}

		$this->mDb->CompleteTrans();
		return $ret;
	}

	function getList( &$pParamHash = NULL ){
		$ret = $bindVars = array();
		$whereSql = "";

		// limit results by content_id
		if( !empty( $pParamHash['content_id'] ) ){
			$bindVars[] = $pParamHash['content_id'];
			$whereSql .= " AND asd.`content_id` = ?";
		} elseif ( $this->isValid() ) {
			$bindVars[] = $this->mContentId;
			$whereSql .= " AND asd.`content_id` = ?";
		}

		/* =-=- CUSTOM BEGIN: getList -=-= */
		// limit results by group_id
		if( !empty( $pParamHash['group_id'] ) ){
			$bindVars[] = $pParamHash['group_id'];
			$whereSql .= " AND asd.`group_id` = ?";
		}

		// limit results by user_id
		if( !empty( $pParamHash['user_id'] ) ){
			$bindVars[] = $pParamHash['user_id'];
			$whereSql .= " AND asd.`user_id` = ?";
		}

		// limit results by content_type_guid
        if( !empty( $pParamHash['content_type_guid'] ) ){
            $bindVars[] = $pParamHash['content_type_guid'];
            $whereSql .= " AND lc.`content_type_guid` = ?";
        }

		/* =-=- CUSTOM END: getList -=-= */

		if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );
		}

		$query = "SELECT  asd.`content_id`, asd.`group_id`, asd.`user_id`, lc.`content_type_guid` 
					FROM `account_security_data` asd  
					INNER JOIN `liberty_content` lc ON ( lc.`content_id` = asd.`content_id`)
					$whereSql";

		$ret = $this->mDb->getArray( $query, $bindVars );
		return $ret;
	}

	/**
	 * preview prepares the fields in this type for preview
	 */
	 function previewFields( &$pParamHash ) {
		$this->prepVerify($pParamHash);
		if (!empty($pParamHash['account_security_data'])) {
			LibertyValidator::preview(
				$this->mVerification['account_security_data'],
				$pParamHash['account_security_data'],
				$this->mInfo);
		}
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateFields( &$pParamHash ) {
		$this->prepVerify($pParamHash);
		if (!empty($pParamHash['account_security_data'])) {
			foreach ($pParamHash['account_security_data'] as $key => $data) {
				$pParamHash['account_security_store'][$key] = array();
				$store[$key] = array();
				LibertyValidator::validate(
					$this->mVerification['account_security_data'],
					$data,
					$this->mErrors, 
					$pParamHash['account_security_store'][$key],
					$this);
			}
		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify(&$pParamHash) {
		if (empty($this->mVerification['account_security_data'])) {

	 		/* Validation for group_id */
			$this->mVerification['account_security_data']['reference']['group_id'] = array(
				'name' => 'Group Id',
				'table' => 'users_groups',
				'column' => 'group_id',
				'required' => '1'
			);
	 		/* Validation for user_id */
			$this->mVerification['account_security_data']['reference']['user_id'] = array(
				'name' => 'User Id',
				'table' => 'users_users',
				'column' => 'user_id',
				'required' => '1'
			);

		}
	}

	/**
	 * returns the data schema by database table
	 */
	public function getSchema() {
		if (empty($this->mSchema['account_security_data'])) {

	 		/* Schema for group_id */
			$this->mSchema['account_security_data']['group_id'] = array(
				'name' => 'group_id',
				'type' => 'reference',
				'label' => 'Group Id',
				'help' => '',
				'table' => 'users_groups',
				'column' => 'group_id',
				'required' => '1'
			);
	 		/* Schema for user_id */
			$this->mSchema['account_security_data']['user_id'] = array(
				'name' => 'user_id',
				'type' => 'reference',
				'label' => 'User Id',
				'help' => '',
				'table' => 'users_users',
				'column' => 'user_id',
				'required' => '1'
			);
		}


		return $this->mSchema;
	}


    /**
     * Check mContentId to establish if the object has been loaded with a valid record
     */
    function isValid() {
        return( BitBase::verifyId( $this->mContentId ) );
    }


    /**
     * setServiceContent
     */
    function setServiceContent( &$pObject ){
        $this->mServiceContent = &$pObject;
    }


	// Getters for reference column options - return associative arrays formatted for generating html select inputs
	function getGroupIdOptions( &$pParamHash=array() ){
		$bindVars = array();
		$joinSql = $whereSql = "";
		/* =-=- CUSTOM BEGIN: group_id_options -=-= */

		/* =-=- CUSTOM END: group_id_options -=-= */
		$query = "SELECT a.group_id, b.title FROM users_groups a INNER JOIN liberty_content b ON a.content_id = b.content_id $joinSql $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}

	function getUserIdOptions( &$pParamHash=array() ){
		$bindVars = array();
		$joinSql = $whereSql = "";
		/* =-=- CUSTOM BEGIN: user_id_options -=-= */

		/* =-=- CUSTOM END: user_id_options -=-= */
		$query = "SELECT a.user_id, b.title FROM users_users a INNER JOIN liberty_content b ON a.content_id = b.content_id $joinSql $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}




	// {{{ =================== Custom Helper Mthods  ====================


	/* This section is for any helper methods you wish to create */
	/* =-=- CUSTOM BEGIN: methods -=-= */

	/* =-=- CUSTOM END: methods -=-= */


	// }}} -- end of Custom Helper Methods


}

function account_security_content_list_sql( $pObject, &$pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_ACCOUNT_SECURITY ) ){
		/* =-=- CUSTOM BEGIN: account_security_content_list_sql -=-= */
		global $gAccount, $gBitUser, $gBitSystem;

		// Find the groups for this user
		$groups = array_keys($gBitUser->mGroups);
		// UserId alias for readability
		$userId = $gBitUser->mUserId;

		// Check that these are all integers just for safety. Assumes they have at least one group but all should have -1
		if ($gBitSystem->verifyId($groups) && $gBitSystem->verifyId($userId)) {

			// get permissions for all content types except bituser
			if( $pObject->mContentTypeGuid != BITUSER_CONTENT_TYPE_GUID ){

				// Debug select
				/*
				$ret['select_sql'] =
					", as_lcpm.`perm_name` AS as_target_perm".
					", as_scd.subproject_content_id AS as_scid".
					", as_asd.`group_id` AS as_group_id, as_asd.`user_id` AS as_user_id ".
					", as_ugpgc.`perm_name` AS as_grant_perm".
					", as_ugpgc.`group_id` AS ugpgc_group".
					", as_dflt.`perm_name` AS as_dflt_perm".
					"";
				*/

				$ret['join_sql'] =
					// Find the permision name
					" LEFT JOIN `".BIT_DB_PREFIX."liberty_secure_permissions_map` as_lcpm ON ( as_lcpm.`content_type_guid` = lc.`content_type_guid` AND as_lcpm.`perm_type` = 'view' )".
					// What subproject is this content in.
					" LEFT JOIN `".BIT_DB_PREFIX."subproject_content_data` as_scd ON (lc.`content_id` = as_scd.`content_id` )".			// content_id is the listed content
					" LEFT JOIN `".BIT_DB_PREFIX."subproject_data` as_sd ON (as_scd.`subproject_content_id` = as_sd.`content_id` )".	// content_id is the associated subproject content_id
					// Find the group for that subproject's account
					" LEFT JOIN `".BIT_DB_PREFIX."account_security_data` as_asd 
								ON (
									(
										as_sd.`content_id` = as_asd.`content_id` 
										OR as_sd.`project_content_id` = as_asd.`content_id` 
										OR as_sd.`account_content_id` = as_asd.`content_id` 
									)
									AND (as_asd.`user_id` = ".$userId." OR as_asd.`user_id` = ".ANONYMOUS_USER_ID." ) 
								   )".		

					// Check if a group is allowed by default
					" LEFT JOIN `".BIT_DB_PREFIX."users_group_permissions` as_dflt ON (as_dflt.`perm_name` = as_lcpm.`perm_name` AND as_dflt.`group_id` IN (".implode(',', $groups) .") )".
					// Check if subproject group is allowed
					" LEFT JOIN `".BIT_DB_PREFIX."users_group_permissions` as_ugpgc ON (as_ugpgc.`perm_name` = as_lcpm.`perm_name` AND as_ugpgc.`group_id` = as_asd.`group_id` )";

				// Only where the permission is granted by default or by account
				// TODO: Teach this to play nice with liberty_security
				$ret['where_sql'] = " AND ( lc.`user_id` = ? OR as_ugpgc.`perm_name` IS NOT NULL OR as_dflt.`perm_name` IS NOT NULL )";
				$ret['bind_vars'] = array( $userId );

			// @TODO move this to new account user class and manage users internally!
			// This is to solve need to jail lists in users pkg - hackish limit to gAccount
			// object checking permission on is bituser we need special rules
			}elseif( $pObject->mContentTypeGuid == BITUSER_CONTENT_TYPE_GUID && is_object( $gAccount ) && $gAccount->isValid()){
				// modify the groups list with our account roles
				$groups = array_merge( $groups, array( $gBitSystem->getConfig('accounts_account_admin_role'), $gBitSystem->getConfig('accounts_account_manager_role'), $gBitSystem->getConfig('accounts_account_member_role') ) ); 
				$ret['join_sql'] = 
					// Find the permision name
					" LEFT JOIN `".BIT_DB_PREFIX."liberty_secure_permissions_map` as_lcpm ON ( as_lcpm.`content_type_guid` = lc.`content_type_guid` AND as_lcpm.`perm_type` = 'view' )".
					// Check if a group is allowed 
					" LEFT JOIN `".BIT_DB_PREFIX."users_group_permissions` as_ugp ON (as_ugp.`perm_name` = as_lcpm.`perm_name` AND as_ugp.`group_id` IN (".implode(',', $groups) .") )";

				// Only where the permission is granted by default or by account
				// TODO: Teach this to play nice with liberty_security
				$ret['where_sql'] = " AND ( lc.`user_id` = ? OR as_ugp.`perm_name` IS NOT NULL )";
				$ret['bind_vars'] = array( $userId );
			}

		}
		/* =-=- CUSTOM END: account_security_content_list_sql -=-= */
		return $ret;	}
}
function account_security_content_user_perms( $pObject, &$pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_ACCOUNT_SECURITY ) ){
		/* =-=- CUSTOM BEGIN: account_security_content_user_perms -=-= */
			global $gBitUser, $gBitSystem, $gAccount;		

			$membership_group_id = $gBitSystem->getConfig('account_membership_group_id', -1);
			if (!empty($membership_group_id) ) {
				$groups = array();

				// Prevent null userId;
				$userId = ( !empty( $pParamHash['content_permissions_user_id'] ) && empty( $_REQUEST['content_permissions_user_id'] ) ) ? $pParamHash['content_permissions_user_id'] : $gBitUser->mUserId;
				if( !is_numeric( $userId ) ) $userId = 0;

				// Find the groups for this content and user
				$apsTypes = array( 'bitaccount', 'bitproject', 'bitsubproject' );

				// @TODO move this to new account user class and manage users internally!
				// user objects
				if( $pObject->mContentTypeGuid == BITUSER_CONTENT_TYPE_GUID ){
					// try by gAccount
					// @TODO this is partly what needs expansiion - should be checking by account, project, and subproject
					// For now we're only checking if the user to be edited is in gAccount
					if( is_object( $gAccount ) ) {
						if( $pObject->isValid() ){
							$query = 
								"SELECT asd.`content_id`, asd.`group_id` FROM `".BIT_DB_PREFIX."account_security_data` asd ".
								"INNER JOIN `".BIT_DB_PREFIX."account_security_data` asd2 ON ( asd.`content_id` = asd2.`content_id` ) ".
								"WHERE ( asd2.`user_id` = ? AND asd2.`content_id` = ? ) ".
								"AND ( asd.`user_id` = ? OR asd.`user_id` = ? )";
							$bindVars = array( $pObject->mUserId, $gAccount->mContentId, $userId, ANONYMOUS_USER_ID );
							$groups = $pObject->mDb->getAssoc($query, $bindVars);
						}else{
						}
					}
				}
				// @TODO = refactor - these queries can be built up from like parts 
				// content management objects
				elseif( in_array( $pObject->mContentTypeGuid, $apsTypes ) || !$pObject->isValid() ){
					// try to determine a subproject content id - preference is give to request 
					if( !empty( $pParamHash['connect_subproject_content_id'] ) ){
						$subproject_content_id = $pParamHash['connect_subproject_content_id'];
					}
					// try by gAccount
					elseif( is_object( $gAccount ) ) {
						if( $gAccount->isValid() ) {
							$subproject_content_id = $gAccount->getPreference( 'default_subproject_content_id' );
						// special case where gAccount is not loaded yet
						}elseif( isset( $gAccount->mContentId ) ){
							$query = "SELECT pref_value FROM  `".BIT_DB_PREFIX."liberty_content_prefs` WHERE content_id = ? and pref_name = ?";
							$bindVars = array( $gAccount->mContentId, 'default_subproject_content_id' );
							$subproject_content_id = $gBitSystem->mDb->getOne( $query, $bindVars );
						}
					}
					// we have a subproject id to get roles based on
					if( !empty( $subproject_content_id ) ){
						$query = 
							"SELECT asd.`content_id`, asd.`group_id` FROM `".BIT_DB_PREFIX."account_security_data` asd ".
							"INNER JOIN `".BIT_DB_PREFIX."subproject_data` sd ON (asd.`content_id` = sd.`content_id` OR asd.`content_id` = sd.`account_content_id` OR asd.`content_id` = sd.`project_content_id`) ".
							"WHERE ( asd.`user_id` = ? OR asd.`user_id` = ? ) ".
							"AND sd.`content_id` = ? ";
						$bindVars = array($userId, ANONYMOUS_USER_ID, $subproject_content_id );
						$groups = $pObject->mDb->getAssoc($query, $bindVars);
					}
				}
				// mapped content
				else{
					$query = 
						"SELECT asd.`content_id`, asd.`group_id` FROM `".BIT_DB_PREFIX."account_security_data` asd ".
						"INNER JOIN `".BIT_DB_PREFIX."subproject_data` sd ON (asd.`content_id` = sd.`content_id` OR asd.`content_id` = sd.`account_content_id` OR asd.`content_id` = sd.`project_content_id`) ".
						"INNER JOIN `".BIT_DB_PREFIX."subproject_content_data` scd ON (sd.`content_id` = scd.`subproject_content_id` )".
						"WHERE ( asd.`user_id` = ? OR asd.`user_id` = ? ) ".
						"AND scd.`content_id` = ?";
					$bindVars = array($userId, ANONYMOUS_USER_ID, $pObject->mContentId);
					$groups = $pObject->mDb->getAssoc($query, $bindVars);
				}

				// get permissions
				if (!empty($groups)) {
					$query = ""; $bindVars = array();
					// valid content object
					if( $pObject->isValid() ){
						$query =
							"SELECT ugp.`perm_name` as `hash_key`, 1 as `group_perm`, ugp.`perm_name`, ugp.`perm_value`, ugp.`group_id` ".
							"FROM `".BIT_DB_PREFIX."users_group_permissions` ugp ".
							"LEFT JOIN `".BIT_DB_PREFIX."liberty_content_permissions` lcp ON (lcp.`group_id`=ugp.`group_id` AND lcp.`content_id`=? AND ugp.`perm_name`=lcp.`perm_name`) ".
							"WHERE lcp.`perm_name` IS NULL AND ugp.`group_id` IN ".
							"( ".implode( ',',array_fill( 0, count( $groups ),'?' ) )." ) ";
						$bindVars = array_merge(array($pObject->mContentId), $groups);
					}
					// new content
					else{
						// @TODO it appears this IN can be dropped and the list of groups achieved by joining to the account_security_data table directly
						$query =
							"SELECT ugp.`perm_name` as `hash_key`, 1 as `group_perm`, ugp.`perm_name`, ugp.`perm_value`, ugp.`group_id` ".
							"FROM `".BIT_DB_PREFIX."users_permissions` up ".
							"INNER JOIN `".BIT_DB_PREFIX."users_group_permissions` ugp ON ( ugp.`perm_name`=up.`perm_name` ) ".
							"WHERE ugp.`group_id` IN ".
							"( ".implode( ',',array_fill( 0, count( $groups ),'?' ) )." ) ";
						$bindVars = $groups;
					}
					
					$accessPerms = $pObject->mDb->getAssoc( $query, $bindVars );

					if ( !empty($accessPerms) ) {
						if( !empty( $pParamHash['content_permissions'] ) ){
							$pParamHash['perms'] = $accessPerms;
						}
						if( !empty( $pParamHash['user_permissions'] ) ){
							$pParamHash['perms'] = !empty( $pParamHash['perms'] )?array_merge( $accessPerms, $pParamHash['perms'] ):$accessPerms;
						}
					}
				}
			}

		/* =-=- CUSTOM END: account_security_content_user_perms -=-= */	}
}
function account_security_content_expunge( $pObject, $pParamHash ){
		vd( 'in service');
	if( $pObject->hasService( LIBERTY_SERVICE_ACCOUNT_SECURITY ) ){
		$account_security = new AccountSecurity( $pObject->mContentId );
		$account_security->setServiceContent( $pObject );  
		if( !$account_security->expunge() ){
			$pObject->setError( 'account_security', $account_security->mErrors );
		}	}
}
