<?php
/**
 * @version $Header$
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => ACCOUNTS_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "This upgrade registers new api hooks.",
	'post_upgrade' => NULL,
);
$gBitInstaller->registerPackageUpgrade( $infoHash, array(
// copy data into new column
array( 'QUERY' =>
    array(
        'SQL92' => array( 
			"INSERT INTO `package_plugins_api_map` ( plugin_guid, api_hook, api_type, plugin_handler ) VALUES ( 'account_security','content_expunge','function','account_security_content_expuge' )", 
		),
    ),
),
));

