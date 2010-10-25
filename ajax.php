<?php
  // Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'accounts' );

if( !empty( $_POST['req'] ) ){
  $gBitSmarty->assign( 'req', $_POST['req'] );
  switch( $_POST['req'] ){
  case 'get_available_projects':
    // Load up the requested account.
    if ( !empty( $_POST['account_content_id'] ) ) {
      require_once(ACCOUNTS_PKG_PATH."BitAccount.php");
      require_once(ACCOUNTS_PKG_PATH."BitSubProject.php");
      $account = new BitAccount( NULL, $_POST['account_content_id'] );
      $account->load();
      if ($account->isValid()) {
	$account->verifyUserPermission( 'p_subproject_create' );
	$subproject = new BitSubProject();
	$options = $subproject->getProjectNameOptions($_POST);
	$options_list = array( ''=>tra('Select one...') );
	foreach( $options as $key=>$value ){
	  $options_list[$key] = $value;
	}	
	$gBitSmarty->assign_by_ref( 'project_id_options', $options_list );
      } else {
	$gBitSmarty->assign('error', "No such account.");
      }
    } else {
      $gBitSmarty->assign('error', "No account requested.");
    }
    break;
  default:
    $gBitSmarty->assign('error', "Invalid Request");
  }
} else {
  $gBitSmarty->assign('error', "Invalid Request");
}

$gBitSystem->display('bitpackage:accounts/ajax.tpl', null, array( 'format' => 'center_only', 'display_mode' => 'edit' ));
die;

