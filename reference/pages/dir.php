<?php

  $title = $dir;
  $intro = '';
  
  $show_php = FALSE;
  $staff    = FALSE;
  $demo     = FALSE;

  $item = padDirList ( PAD . "reference/pages/$dir" );

  $padFile = PAD . "reference/pages/$dir/dir.pad";

  if ( file_exists($padFile) )
    foreach ( file($padFile, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = padExplode ($line, ':');
      if ($cmd[0] == 'intro') $intro = $cmd[1];
      if ($cmd[0] == 'title') $title = $cmd[1];
      if ($cmd[0] == 'list' ) $item  = padExplode ($cmd[1], ',');
      if ($cmd[0] == 'flag' ) {$fld  = $cmd[1]; $$fld = TRUE;}
    }

  foreach ( $item as $one )
    if ( file_exists ( PAD . "reference/pages/$dir/$one.php" ) )
      $show_php = TRUE;

?>