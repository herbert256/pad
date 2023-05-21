<?php

  $showTitle = FALSE;

  $htmlOld = padApp . "_regression/$item.html";
  $htmlNew = padApp . "$item.html";

  $old = padFileGetContents ( $htmlOld );

  $new = getPage ($item);
  $new = $new ['data'];

  $diff = diff ( $old, $new );

  $demoModeLines = [];

  $demoMode = FALSE;

  if ( strpos($new, '<!-- START DEMO RESULT -->') ) {

    $oldRes = $newRes = $newSrc = [];

    $check = $new;
    while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
      $newRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

    $check = $old;
    while ( strpos($check, '<!-- START DEMO RESULT -->') ) 
      $oldRes [] = trim ( padCut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

    $html = padFileGetContents ( padApp . "$item.html" );
    while ( strpos($html, '{demo}') )
      $newSrc [] = trim ( padCut ( $html, '{demo}', '{/demo}' ) );

    $check = $new;
    while ( strpos($check, '<!-- START DEMO SOURCE -->') ) 
      $newSrc [] = trim ( padCut ( $check, '<!-- START DEMO SOURCE -->', '<!-- END DEMO SOURCE -->' ) );

    $demoMode = FALSE;
    $compare  = [];

    if ( count($oldRes) and count ($oldRes) == count($newRes) and count($oldRes) == count($newSrc)) 
     foreach ( $oldRes as $key => $value ) 
        if ( $oldRes [$key] <> $newRes [$key] and strpos ($newSrc [$key], 'random') === FALSE and strpos ($newSrc [$key], 'shuffle') === FALSE ) {
          $demoMode = TRUE;
          $compare [$key] ['diff'] = diff ( $oldRes [$key], $newRes [$key] );
        } else {
          unset ( $oldRes [$key] );
          unset ( $newRes [$key] );
        }

    if ( $demoMode) {

      $demoModeLines = [];

      foreach ( $oldRes as $key => $value ) {
        $demoModeLines [$key] ['newSrc'] = $newSrc [$key];
        $demoModeLines [$key] ['oldRes'] = $oldRes [$key];
        $demoModeLines [$key] ['newRes'] = $newRes [$key];
      }


    }

  }

  $onlyResult = onlyResult ($htmlNew);

?>