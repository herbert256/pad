<?php

  $title = $padPage;
  $title = str_replace ( '/index', '', $title );
  $title = padExplode ( $title, '/' );
  $title = end ( $title ) ;

?>