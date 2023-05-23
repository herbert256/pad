<?php

  $item  = padPageGetName ($item);
  $title = $item;
  $new   = getPageData ($item);
  $old   = padFileGetContents ( padApp . "_regression/$item.html" );
  $html  = padFileGetContents ( padApp . "$item.html" );
  $diff  = diff ( $old, $new );

  if ( $old == $new OR strpos ( $new, '<!-- PAD: NO REGRESSION -->' ) !== FALSE ) {
    $changed = FALSE;
    return;
  }

  $changed = TRUE;

  $oldRes = $newRes = $newSrc = [];

  $check = $new;
  while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
    $newRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

  $check = $old;
  while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
    $oldRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

  $check = $new;
  while ( strpos($check, '<!-- START DEMO SOURCE -->') ) 
    $newSrc [] = trim ( padCut ( $check, '<!-- START DEMO SOURCE -->', '<!-- END DEMO SOURCE -->' ) );

  $demoMode = FALSE;

  if ( count($oldRes) and count ($oldRes) == count($newRes) and count($oldRes) == count($newSrc)) {
   foreach ( $oldRes as $key => $value ) {
     if ( strpos ($newSrc [$key], 'random') === FALSE and strpos ($newSrc [$key], 'shuffle') === FALSE ) {}
       if ( $oldRes [$key] <> $newRes [$key] ) {
        $demoMode = TRUE;
        $compare   [$key] ['diff']   = diff ( $oldRes [$key], $newRes [$key] );
        $demoLines [$key] ['newSrc'] = $newSrc [$key];
        $demoLines [$key] ['oldRes'] = $oldRes [$key];
        $demoLines [$key] ['newRes'] = $newRes [$key];
      }
    }
  }

?>