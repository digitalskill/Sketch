<?php
class IMAGES extends CONTROLLER {
	function IMAGES( $page ) {
		$output    = 'var tinyMCEImageList = new Array(';
		$directory = sketch( "abspath" ) . "sketch-images/";
		$im        = $this->getImages( $directory );
		if ( $im != "" ) {
			$im = substr( $im, 0, -1 );
			$im .= "\n";
		}
		$output = $output . $im . ');';
		header( 'Content-type: text/javascript' );
		echo str_replace( "../", "", $output );
	}
	function getImages( $directory ) {
		$outstring = '';
		$delimiter = "\n";
		if ( is_dir( $directory ) ) {
			$direc = opendir( $directory );
			while ( $file = readdir( $direc ) ) {
				if ( !preg_match( '~^\.~', $file ) ) {
					if ( is_file( "$directory/$file" ) ) {
						$outstring .= $delimiter . '["' . utf8_encode( end( explode( "/sketch-images/", "$directory/$file" ) ) ) . '", "' . utf8_encode( ( "/sketch-images/" . str_replace("//","/",end( explode( "/sketch-images/", "$directory/$file" )  ) ) ) ). '"],';
					} else {
						if ( is_dir( "$directory/$file" ) ) {
							$outstring .= $this->getImages( "$directory/$file" );
						}
					}
				}
			}
			closedir( $direc );
		}
		return $outstring;
	}
}