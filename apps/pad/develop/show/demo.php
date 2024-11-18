<?php

  include APP . 'develop/show/_includes/shared.php';

  $compare = $demoLines = [];
  
  foreach ( $oldRes as $key => $value ) 
    if ( isset ($newRes [$key]) and $oldRes [$key] <> $newRes [$key] ) {
      $compare   [$key] ['diff']   = diff ( $oldRes [$key], $newRes [$key] );
      $demoLines [$key] ['newSrc'] = $newSrc [$key];
      $demoLines [$key] ['oldRes'] = $oldRes [$key];
      $demoLines [$key] ['newRes'] = $newRes [$key];
    }

?>