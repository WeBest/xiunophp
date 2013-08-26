<?php

/*
 * Copyright (C) xiuno.com
 */

function opendir_recursive($dir, $recall, $skip_keywords = array()) {
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false ) {
				if( $file != "." && $file != ".." && !in_array($file, $skip_keywords)) {
					if(is_dir( $dir . $file)) {
				    		call_user_func_array($recall, array($dir . $file, true));
					    		opendir_recursive( $dir . $file . "/", $recall);
					    } else {
						    call_user_func_array($recall, array($dir . $file, false));
					    }
				}
			}
			closedir($dh);
		 }
	 }
}

$content = '';
function doit($file, $isdir) {
	global $content;
	$fp = fopen("./1.htm", 'ab+');
	if(!$isdir && substr($file, -4) == '.php') {
		fwrite($fp, '<h3>'.$file.'</h3>'.highlight_string(file_get_contents($file), 1));
		fclose($fp);
	}
}

file_put_contents('./1.htm', '<h1>XiunoBBS 源代码</h1>');
opendir_recursive('E:/www/xiuno.2.0.0/', 'doit', array('.svn'));


?>
