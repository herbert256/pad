<?php

  $app = 'pad';

  $script = $_SERVER ['SCRIPT_NAME'] ?? '';
  $dir    = dirname ( $script );
  $parts  = explode ( '/', $dir );

  foreach ( $parts as $part ) 
    if ( $part and file_exists ( "$padHome/apps/$part") ) {
      $app = $part;
      break;
    }

  define ( 'APP', "$padHome/apps/$app/" );
  
  unset ( $app, $script, $dir, $parts, $part );

?>