<?php
  
  $title = str_replace ( '/index', '', $padPage );
  $title = padExplode ( $title, '/' );
  $title = end ( $title ) ;
  $title = str_replace ( '_', ' ', $title );
  $title = ucwords ( $title );

  $showTitle = TRUE;

?>