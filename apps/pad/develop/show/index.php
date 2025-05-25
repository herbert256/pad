<?php

  $curl = getPage ($item, 1);

  if ( ! str_starts_with ( $curl ['result'], '2') )
    padRedirect ($item);

  $oldRes = $newRes = $newSrc = $compare = $demoLines = [];
 
  $title = $item;
  $new   = $curl ['data'];
  $old   = padFileGetContents ( APP . "_regression/$item.html" );
  $diff  = diff ( $old, $new );
   
  $check = $old;
  while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
    $oldRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

  $check = $new;
  while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
    $newRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );
  
  $check = $new;
  while ( strpos($check, '<!-- START DEMO SOURCE -->') ) 
    $newSrc [] = trim ( padCut ( $check, '<!-- START DEMO SOURCE -->', '<!-- END DEMO SOURCE -->' ) );
 
  foreach ( $oldRes as $key => $value ) 

    if ( isset ($newRes [$key]) and $oldRes [$key] <> $newRes [$key] ) {
      $compare   [$key] ['diff']   = diff ( $oldRes [$key], $newRes [$key] );
      $demoLines [$key] ['newSrc'] = $newSrc [$key];
      $demoLines [$key] ['oldRes'] = $oldRes [$key];
      $demoLines [$key] ['newRes'] = $newRes [$key];
    }
  
?>