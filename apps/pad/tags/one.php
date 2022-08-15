<?php

  $one = $pPrm [$p];
  
  $url = "$pHost$pScript?app=reference&page=$one";

  $pFile  = APPS . "reference/pages/$one.pad";
  $php_file  = APPS . "reference/pages/$one.php";
  $html_file = APPS . "reference/pages/$one.html";

  $dirx = substr($one, 0, strrpos($one, '/')+1 ); 
  $link = str_replace($dirx, '', $one);

  if ( file_exists($pFile) )
    foreach ( file($pFile, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = pExplode ($line, ':');
      if ($cmd[0] == 'php')
         $php_file = APPS . 'reference/' . $cmd[1];
      if ($cmd[0] == 'html')
         $html_file = APPS . 'reference/' .  $cmd[1];
    }

  $php_data  = ( file_exists($php_file ) ) ? pColors_file ($php_file ) : '';
  $html_data = ( file_exists($html_file) ) ? pColors_file ($html_file) : '';

  $php_file  = str_replace(APPS.'reference/', '', $php_file);  
  $html_file = str_replace(APPS.'reference/', '', $html_file);

  $result = pad ('reference', $one);

  if ( strpos($result, '<!-- demo -->' ))
    $demo = TRUE;

?>