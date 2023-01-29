<?php

  $one = $padPrm [$pad] [0];
  
  $url = "$padHost$padScript?app=reference&page=$one";

  $padFile  = PAD . "pad/reference/pages/$one.pad";
  $php_file  = PAD . "pad/reference/pages/$one.php";
  $html_file = PAD . "pad/reference/pages/$one.html";

  $dirx = substr($one, 0, strrpos($one, '/')+1 ); 
  $link = str_replace($dirx, '', $one);

  if ( file_exists($padFile) )
    foreach ( file($padFile, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = padExplode ($line, ':');
      if ($cmd[0] == 'php')
         $php_file = PAD . 'reference/' . $cmd[1];
      if ($cmd[0] == 'html')
         $html_file = PAD . 'reference/' .  $cmd[1];
    }

  $php_data  = ( file_exists($php_file ) ) ? padColorsFile ($php_file ) : '';
  $html_data = ( file_exists($html_file) ) ? padColorsFile ($html_file) : '';

  $php_file  = str_replace(PAD . 'pad/reference/', '', $php_file);  
  $html_file = str_replace(PAD . 'pad/reference/', '', $html_file);

  $result = pad ('reference', $one);

  if ( strpos($result, '<!-- demo -->' ))
    $demo = TRUE;

?>