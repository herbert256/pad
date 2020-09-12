<?php

  if ( substr($page,-6 ) == '/index' )
    $mode = 'demo';
  else
    $mode = '';

  $title = substr($page, 0, -6);

  if ( file_exists(PAD_HOME. "tags/$title.php") )
    $title = "&open;$title&close;";
  else {
    $title = str_replace('_', ' ', $title);
    $title = ucwords($title);
  }

?>