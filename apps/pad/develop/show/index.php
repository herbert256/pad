<?php

  include APP . 'develop/show/_includes/shared.php';

  if ( count($oldRes) ) 
    if ( count ($oldRes) == count($newRes) and count($oldRes) == count($newSrc) ) 
      foreach ( $oldRes as $key => $value ) 
        if ( strpos ($newSrc [$key], 'random') === FALSE and strpos ($newSrc [$key], 'shuffle') === FALSE ) 
          if ( $oldRes [$key] <> $newRes [$key] )
            padRedirect ("develop/show/demo&item=$item");

  if ( $old <> $new and ! strpos ( $new, '<!-- PAD: NO REGRESSION -->' ) !== FALSE )
    padRedirect ("develop/show/changed&item=$item");

  $showTitle = TRUE;
  $title     = $item;
  
?>