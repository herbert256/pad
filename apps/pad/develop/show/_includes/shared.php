<?php

  $curl = getPage ($item, 1);

  if ( ! str_starts_with ( $curl ['result'], '2') )
    padRedirect ("$item&noShow=1");

  $title = $item;
  $new   = $curl ['data'];
  $old   = padFileGetContents ( APP . "_regression/$item.html" );
 
  $oldRes = $newRes = $newSrc = [];
  
  $check = $old;
  while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
    $oldRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

  $check = $new;
  while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
    $newRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );
  
  $check = $new;
  while ( strpos($check, '<!-- START DEMO SOURCE -->') ) 
    $newSrc [] = trim ( padCut ( $check, '<!-- START DEMO SOURCE -->', '<!-- END DEMO SOURCE -->' ) );

?>