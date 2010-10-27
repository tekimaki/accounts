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
* SubProjectContent class
* Stores content within a subproject
*
* @version $Revision: $
* @class SubProjectContent
*/

/**
 * Initialize
 */
require_once( LIBERTY_PKG_PATH.'LibertyBase.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */

/* =-=- CUSTOM END: require -=-= */

class SubProjectContent extends LibertyBase {

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
	 * stores a single record in the subproject_content table
	 */
	function store( &$pParamHash ){
		if( $this->verify( $pParamHash ) ) {
			if ( !empty( $pParamHash['subproject_content_store'] ) ){
				$table = 'subproject_content_data';
				$this->mDb->StartTrans();
				foreach ($pParamHash['subproject_content_store'] as $key => $data) {
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
	 * verifies a data set for storage in the Subproject_content table
	 * data is put into $pParamHash['subproject_content_store'] for storage
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

		$query = "DELETE FROM `subproject_content` ".$whereSql;

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
			$whereSql = " AND `subproject_content_data`.content_id = ?";
		} elseif ( $this->isValid() ) {
			$bindVars[] = $this->mContentId;
			$whereSql = " AND `subproject_content_data`.content_id = ?";
		}

		/* =-=- CUSTOM BEGIN: getList -=-= */

		/* =-=- CUSTOM END: getList -=-= */

		if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );
		}

		$query = "SELECT  `subproject_content_id` FROM `subproject_content_data`".$whereSql;
		$ret = $this->mDb->getArray( $query, $bindVars );
		return $ret;
	}

	/**
	 * preview prepares the fields in this type for preview
	 */
	 function previewFields( &$pParamHash ) {
		$this->prepVerify();
		if (!empty($pParamHash['subproject_content_data'])) {
			LibertyValidator::preview(
				$this->mVerification['subproject_content_data'],
				$pParamHash['subproject_content_data'],
				$this, $pParamHash['subproject_content_store']);
		}
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateFields( &$pParamHash ) {
		$this->prepVerify();
		if (!empty($pParamHash['subproject_content_data'])) {
			foreach ($pParamHash['subproject_content_data'] as $key => $data) {
				$pParamHash['subproject_content_store'][$key] = array();
				$store[$key] = array();
				LibertyValidator::validate(
					$this->mVerification['subproject_content_data'],
					$data,
					$this, $pParamHash['subproject_content_store'][$key]);
			}
		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify() {
		if (empty($this->mVerification['subproject_content_data'])) {

	 		/* Validation for subproject_content_id */
			$this->mVerification['subproject_content_data']['reference']['subproject_content_id'] = array(
				'name' => 'Sub Projects',
				'table' => 'liberty_content',
				'column' => 'content_id',
				'required' => '1'
			);

		}
	}

	/**
	 * returns the data schema by database table
	 */
	public function getSchema() {
		if (empty($this->mSchema['subproject_content_data'])) {

	 		/* Schema for subproject_content_id */
			$this->mSchema['subproject_content_data']['subproject_content_id'] = array(
				'name' => 'subproject_content_id',
				'type' => 'reference',
				'label' => 'Sub Projects',
				'help' => 'Select the sub-projects this content belongs to',
				'table' => 'liberty_content',
				'column' => 'content_id',
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
	function getSubProjectsOptions( &$pParamHash=array() ){
		$bindVars = array();
		$joinSql = $whereSql = "";
		/* =-=- CUSTOM BEGIN: subproject_content_id_options -=-= */

		/* =-=- CUSTOM END: subproject_content_id_options -=-= */
		$query = "SELECT a.content_id, b.title FROM subproject_data a INNER JOIN liberty_content b ON a.content_id = b.content_id $joinSql $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}




	// {{{ =================== Custom Helper Mthods  ====================


	/* This section is for any helper methods you wish to create */
	/* =-=- CUSTOM BEGIN: methods -=-= */

	/* =-=- CUSTOM END: methods -=-= */


	// }}} -- end of Custom Helper Methods


}

function subproject_content_content_edit( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
		global $gBitSystem, $gBitSmarty, $gAccount, $gBitUser;

		$subproject_content = new SubProjectContent( $pObject->mContentId );

		// three paths to passing a subproject id to the edit form
		// 1. content has already been mapped
		// 2. user has designated an id to map to
		// 3. user has not designated an id, but gAccount is in effect
		// 4. no id and no gaccount - a list of projects is offered, NOTE: NOT IMPLEMENTED FOR SIMPLICITY AT THIS POINT

		// permission checks happen within the security plugin 

		// 1. content has already been mapped
		if( $pObject->isValid() && $subp_ids = $subproject_content->getList() ) {
			$connect_subproject_content_id = $subp_ids[0];
		}
		// 2. user has designated an id to map to 
		elseif( !empty( $pParamHash['connect_subproject_content_id'] ) ){
			$connect_subproject_content_id = $pParamHash['connect_subproject_content_id'];
		}
		// 3. user has not designated an id, but gAccount is in effect
		elseif( is_object( $gAccount ) && $gAccount->isValid() ) {
			$connect_subproject_content_id = $gAccount->getPreference( 'default_project_id' );
			if( empty( $connect_subproject_content_id ) ){
				if( $gBitUser->isAdmin() ){
					$gBitSystem->fatalError( 'Site configuration error', 'error.tpl', 'No default subproject known for gAccount '.$gAccount->getTitle().'. A default subproject needs to be created for this account.' );	
				}else{
					$gBitSystem->fatalError( 'Site configuration error', 'error.tpl', 'Please report this incident to an administrator.' );
				}
			}
		}
		// Fatal Error - subproject_content_id required
		elseif( $pObject->isServiceRequired( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
			if( $gBitUser->isAdmin() ){
				$gBitSystem->fatalError( 'Site configuration error', 'error.tpl', 'No subproject id set in SubProjectContent::subproject_content_content_edit' );	
			}else{
				$gBitSystem->fatalError( 'Bad Request, Access Denied' );
			}
		}
		// 4. get a select list @TODO this is crude and disabled until the feature is warranted
		else{
		/*
		// Load options for Sub Projects
		$subproject_content_id_options =  $subproject_content->getSubProjectsOptions( $listHash );
		$gBitSmarty->assign('subproject_content_id_options', $subproject_content_id_options);
		break;
		*/
		}
		// pass through to display to load up content data
		subproject_content_content_display( $pObject, $pParamHash );

		$gBitSmarty->assign( 'connect_subproject_content_id', $connect_subproject_content_id );
	}
}
function subproject_content_content_store( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
		// get the subproject id to map too
		if( !empty( $pParamHash['connect_subproject_content_id'] ) ){
			$pParamHash['subproject_content_id'] = $pParamHash['connect_subproject_content_id'];
		}
		elseif( is_object( $gAccount ) && $gAccount->isValid() ) {
			$pParamHash['subproject_content_id'] = $gAccount->getPreference( 'default_project_id' );
		}
		elseif( $pObject->isServiceRequired( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
			if( empty( $connect_subproject_content_id ) ){
				if( $gBitUser->isAdmin() ){
					$gBitSystem->fatalError( 'Site configuration error', 'error.tpl', 'No subproject id set in SubProjectContent::subproject_content_content_store' );	
				}else{
					$gBitSystem->fatalError( 'Bad Request, Access Denied' );
				}
			}
		}

		// store the mapping
		$subproject_content = new SubProjectContent( $pObject->mContentId );
		if( !$subproject_content->store( $pParamHash ) ){
			$pObject->setError( 'subproject_content', $subproject_content->mErrors );
		}
	}
}
function subproject_content_content_expunge( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
		$subproject_content = new SubProjectContent( $pObject->mContentId );
		if( !$subproject_content->expunge() ){
			$pObject->setError( 'subproject_content', $subproject_content->mErrors );
		}	}
}
function subproject_content_content_display( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
		global $gBitSmarty;
		/* NOT NECESSARY - CONSIDER DROPING FROM API HANDLERS
		if( $pObject->isValid() ) {
			$subproject_content = new SubProjectContent();
			$listHash = array( 'content_id' => $pObject->mContentId );
			$pObject->mInfo['subproject_content'] = $subproject_content->getList( $listHash );

		}
		*/
	}
}
function subproject_content_content_preview( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_SUBPROJECT_CONTENT ) ){
		global $gBitSmarty;

		// call edit service which loads any data necessary for form
		subproject_content_content_edit();
		// preview
		$subproject_content = new SubProjectContent();
		$pObject->mInfo['subproject_content'] = $subproject_content->previewFields( $pParamHash );

		// Load options for Sub Projects
		/*
		$subproject_content_id_options =  $subproject_content->getSubProjectsOptions( $listHash );
		$gBitSmarty->assign('subproject_content_id_options', $subproject_content_id_options);
		*/

	}
}
