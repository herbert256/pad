<?php
  
  $item = pad_dir_list ( PAD_APP . "pages/$dir" );

  if ( count($item) == 1 )
    pad_go ( "reference&page=one&one=$dir/" . reset($item) );

  $pad_file = PAD_APP . "pages/$dir/dir.pad";

  if ( pad_file_exists($pad_file) )
    foreach ( file($pad_file, FILE_IGNORE_NEW_LINES) as $line ) {
      $cmd = pad_explode ($line, ':');
      if ($cmd[0] == 'intro') $intro = $cmd[1];
      if ($cmd[0] == 'title') $title = $cmd[1];
      if ($cmd[0] == 'list' ) $item  = pad_explode ($cmd[1], ',');
      if ($cmd[0] == 'flag' ) {$fld  = $cmd[1]; $$fld = TRUE;}
    }

?>