<?php
/**
 * @version $Header$
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => ACCOUNTS_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "This upgrade adds a new provisional lite status and forks from the mainline code.",
	'post_upgrade' => NULL,
);
$gBitInstaller->registerPackageUpgrade( $infoHash, array(
// copy data into new column
array( 'QUERY' =>
    array(
        'SQL92' => array( 
			"INSERT INTO `liberty_content_status` ( content_status_id, content_status_name ) VALUES ( 5,'Provisional Lite' )", 
		),
    ),
),
));

