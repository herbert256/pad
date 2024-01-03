<?php
 
  $for  = '';
  $xref = 'develop';
  $list = scandir ( padApp . '_xref/develop' ); 

  foreach ( $list as $file ) {

    if ( $file == '.'  ) continue;
    if ( $file == '..' ) continue;

    $xdevelop [$file] ['item'] = $file;
  
  }

  $type = '';

?>