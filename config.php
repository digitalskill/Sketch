<?php 
// Setting if the site is subfolder or sub-domain controlled
$site_settings = array();
$site_settings['directory']			= true;		// Set the site to a directory setting (false will use domain names to get websites)
$site_settings['ignore']   			= "";		// Set this to the directory being used to develop the site
$site_settings['version']  			= "3";
$site_settings['PathTosketch']  	= "";		// This must be the server path to folder with the sketch-system file or leave empty for stand alone version
$site_settings['themePath']			= "";		// The default theme path for sketch - use if no Database is present
/* FORCE removal from url
	Use this if the site is in two different locations - BUT using the same database
	Then one of them in a root location may need to ignore the database folder settings
	eg. One server may have the site in a folder and the other does not
*/
$site_settings['remove_from_path'] 	= "";            // Set this value to a folder of path that must not appear in the url

// Cache Scripts
$site_settings['googleapi']       	= true;         // CHANGE this to true to use googles API to load mootools
$site_settings['cache']            	= true;         // Change this true when completed developing CSS and javascript
$site_settings['proxy_css_js']     	= true;			// Change this false if you dont want proxies serving css and javascript pages

// Force WWW in URLS
$site_settings['www']	   			= false;				// This does not redirect pages - It will just add "www" to all pod->urlPath() calls and Style sheet image urls

// Database connection
$site_settings['prefix']			= ""; 					// The Table prefix for the database
$site_settings['hostname']			= "localhost";
$site_settings['database']			= "";
$site_settings['username']			= "";           		//"postgres" | "root"
$site_settings['password']			= "";
$site_settings['dbtype']   			= "mysql";     			//"pgsql" | "mysql" | "No"

// Page Security (BASIC AUTH)
$site_settings['auth'] 				= false;
$site_settings['realm'] 			= "sketch";
$site_settings['auth_username'] 	= '';
$site_settings['auth_password'] 	= '';

// DEBUG
$site_settings['show_php_errors']	= false;		// Set this to True to view php errors in the browser or for debugging

// SITE CAN USE HTACCESS FILE
$site_settings['htaccess'] 			= false;		// SET THIS TO FALSE IF .htaccess does not run

// USE GZIP COMPRESSION
$site_settings['compress']			= true;         // GZIP output to save bandwidth

// SET ENCRYPTION TEXT
define('SALT', '');					 					// Encrypt passwords