<?php

  $one = $pad_parm;
  
  $url = "$pad_host$pad_script?app=reference&page=$one";

  $pad_file  = APPS . "reference/pages/$one.pad";
  $php_file  = APPS . "reference/pages/$one.php";
  $html_file = APPS . "reference/pages/$one.html";

  $dirx = substr($one, 0, strrpos($one, '/')+1 ); 
  $link = str_replace($dirx, '', $one);

  if ( pad_file_exists($pad_file) )
    foreach ( file($pad_file, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = pad_explode ($line, ':');
      if ($cmd[0] == 'php')
         $php_file = APPS . 'reference/' . $cmd[1];
      if ($cmd[0] == 'html')
         $html_file = APPS . 'reference/' .  $cmd[1];
    }

  $php_data  = ( pad_file_exists($php_file ) ) ? pad_colors_file ($php_file ) : '';
  $html_data = ( pad_file_exists($html_file) ) ? pad_colors_file ($html_file) : '';

  $php_file  = str_replace(APPS.'reference/pages/', '', $php_file);  
  $html_file = str_replace(APPS.'reference/pages/', '', $html_file);

  $result = pad ('reference', $one);

  if ( $php_data )
    $show_php = TRUE;

?>