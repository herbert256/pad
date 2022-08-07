<?php

  $title = $dir;
  
  $show_php = FALSE;
  $staff    = FALSE;
  $demo     = FALSE;

  $item = pDir_list ( APPS . "reference/pages/$dir" );

  $pFile = APPS . "reference/pages/$dir/dir.pad";

  if ( file_exists($pFile) )
    foreach ( file($pFile, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = pExplode ($line, ':');
      if ($cmd[0] == 'intro') $intro = $cmd[1];
      if ($cmd[0] == 'title') $title = $cmd[1];
      if ($cmd[0] == 'list' ) $item  = pExplode ($cmd[1], ',');
      if ($cmd[0] == 'flag' ) {$fld  = $cmd[1]; $$fld = TRUE;}
    }

?>