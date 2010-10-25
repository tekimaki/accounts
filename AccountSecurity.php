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
				foreach ($pParamHash['account_security_store'] as $key => $data) {
					if (!empty($pParamHash['content_store']['content_id'])) {
						$data['content_id'] = $pParamHash['content_store']['content_id'];
					} else {
						$data['content_id'] = $this->mContentId;
					}
					if ($this->mDb->getOne("SELECT * from ".$table." WHERE `content_id` = ?"
										   , array($data['content_id']
												 ))) {
						$locId = array( "content_id" => $data['content_id']
										);
						$result = $this->mDb->associateUpdate( $table, $data, $locId );
					} else {
						$result = $this->mDb->associateInsert( $table, $data );
					}
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
			$whereSql .= "`content_id` = ?";
		}

		/* =-=- CUSTOM BEGIN: expunge -=-= */

		/* =-=- CUSTOM END: expunge -=-= */

		if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );
		}

		$query = "DELETE FROM `account_security` ".$whereSql;

		if( $this->mDb->query( $query, $bindVars ) ){
			$ret = TRUE;
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
			$whereSql = " AND `account_security_data`.content_id = ?";
		} elseif ( $this->isValid() ) {
			$bindVars[] = $this->mContentId;
			$whereSql = " AND `account_security_data`.content_id = ?";
		}

		// limit results by group_id
		if( !empty( $pParamHash['group_id'] ) ){
			$bindVars[] = $pParamHash['group_id'];
			$whereSql .= " AND `group_id` = ?";
		}

		// limit results by user_id
		if( !empty( $pParamHash['user_id'] ) ){
			$bindVars[] = $pParamHash['user_id'];
			$whereSql .= " AND `user_id` = ?";
		}

		/* =-=- CUSTOM BEGIN: getList -=-= */

		/* =-=- CUSTOM END: getList -=-= */

		if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );
		}

		$query = "SELECT  `group_id`, `user_id` FROM `account_security_data`".$whereSql;
		$ret = $this->mDb->getArray( $query, $bindVars );
		return $ret;
	}

	/**
	 * preview prepares the fields in this type for preview
	 */
	 function previewFields( &$pParamHash ) {
		$this->prepVerify();
		if (!empty($pParamHash['account_security_data'])) {
			LibertyValidator::preview(
				$this->mVerification['account_security_data'],
				$pParamHash['account_security_data'],
				$this, $pParamHash['account_security_store']);
		}
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateFields( &$pParamHash ) {
		$this->prepVerify();
		if (!empty($pParamHash['account_security_data'])) {
			foreach ($pParamHash['account_security_data'] as $key => $data) {
				$pParamHash['account_security_store'][$key] = array();
				$store[$key] = array();
				LibertyValidator::validate(
					$this->mVerification['account_security_data'],
					$data,
					$this, $pParamHash['account_security_store'][$key]);
			}
		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify() {
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

function account_security_content_list_sql( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_ACCOUNT_SECURITY ) ){
		/* =-=- CUSTOM BEGIN: account_security_content_list_sql -=-= */

		/* =-=- CUSTOM END: account_security_content_list_sql -=-= */
		return $ret;	}
}
function account_security_content_user_perms( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_ACCOUNT_SECURITY ) ){
		/* =-=- CUSTOM BEGIN: account_security_content_user_perms -=-= */
		global $gBitUser, $gBitSystem;		
		$membership_group_id = $gBitSystem->getConfig('account_membership_group_id', -1);
		if (!empty($membership_group_id) && $pObject->isValid()) {
			// Prevent null userId;
			if( !is_numeric( $userId ) ) $userId = 0;
			
			// Find the groups for this content and user
			$query = 
				"SELECT asd.`group_id` FROM `".BIT_DB_PREFIX."account_security_data` asd ".
				"INNER JOIN `".BIT_DB_PREFIX."subproject_data` sd ON (asd.`content_id` = sd.`content_id` OR asd.`content_id` = sd.`account_content_id` OR asd.`content_id` = sd.`project_content_id`) ".
				"INNER JOIN `".BIT_DB_PREFIX."subproject_content_data` scd ON (sd.`content_id` = scd.`content_id` )".
				"WHERE asd.`user_id` = ? OR asd.`user_id` = ? ".
				"AND scd.`content_id` = ?";
			$bindVars = array($userId, ANONYMOUS_USER_ID, $pObject->mContentId);
			$groups = $pObject->mDb->getArray($query, $bindVars);

			if (!empty($groups)) {
				$query =
					"SELECT ugp.`perm_name` as `hash_key`, 1 as `group_perm`, ugp.`perm_name`, ugp.`perm_value`, ugp.`group_id` ".
					"FROM `".BIT_DB_PREFIX."users_group_permissions` ugp ".
					"LEFT JOIN `".BIT_DB_PREFIX."liberty_content_permissions` lcp ON(lcp.`group_id`=ugp.`group_id` AND lcp.`content_id`=? AND ugp.`perm_name`=lcp.`perm_name`) ".
					"WHERE lcp.`perm_name` IS NULL AND ugp.`group_id` IN ".
					"( ".implode( ',',array_fill( 0, count( $groups ),'?' ) ).") ";
				$bindVars = array_merge(array($pObject->mContentId), $groups);
				
				$accessPerms = $this->mDb->getAssoc( $query, $bind_vars );
				if ( !empty($accessPerms) ) {
					$pObject->mUserContentPerms = array_merge($pObject->mUserContentPerms, $accessPerms);
				}
			}
		}

		/* =-=- CUSTOM END: account_security_content_user_perms -=-= */	}
}
