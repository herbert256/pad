<?php
  
  if     ( $padPage == 'reference/reference'   ) $title = $reference;
  elseif ( $padPage == 'reference/show'        ) $title = $item;
  else                                           $title = $padPage;

  $title = str_replace ( '/index', '', $title );
  $title = padExplode ( $title, '/' );
  $title = end ( $title ) ;
  $title = str_replace ( '_', ' ', $title );
  $title = ucwords ( $title );

  $showTitle = TRUE;

?>