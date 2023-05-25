<?php

  include '_includes/shared.php';

  if ( count($oldRes) ) 
    if ( count ($oldRes) == count($newRes) and count($oldRes) == count($newSrc) ) 
      foreach ( $oldRes as $key => $value ) 
        if ( strpos ($newSrc [$key], 'random') === FALSE and strpos ($newSrc [$key], 'shuffle') === FALSE ) 
          if ( $oldRes [$key] <> $newRes [$key] )
            padRedirect ("development/show/demo&item=$item");

  if ( $old <> $new and ! strpos ( $new, '<!-- PAD: NO REGRESSION -->' ) !== FALSE )
    padRedirect ("development/show/changed&item=$item");

  $showTitle = FALSE;
  
?>