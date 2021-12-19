<?php

  $one = $pad_parm;
  
  $url = "$pad_host$pad_script?app=reference&page=$one";

  $pad_file  = PAD_APP . "pages/$one.pad";
  $php_file  = PAD_APP . "pages/$one.php";
  $html_file = PAD_APP . "pages/$one.html";

  $dirx = substr($one, 0, strrpos($one, '/')+1 ); 
  $link = str_replace($dirx, '', $one);

  if ( pad_file_exists($pad_file) )
    foreach ( file($pad_file, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = pad_explode ($line, ':');
      if ($cmd[0] == 'php')
         $php_file = PAD_APP . $cmd[1];
      if ($cmd[0] == 'html')
         $html_file = PAD_APP . $cmd[1];
    }

  $php_data  = ( pad_file_exists($php_file ) ) ? pad_colors_file ($php_file ) : '';
  $html_data = ( pad_file_exists($html_file) ) ? pad_colors_file ($html_file) : '';

  $curl   = pad_include ("reference&page=$one");
  $result = $curl ['data'];

?>