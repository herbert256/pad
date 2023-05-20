<?php

  $showTitle = FALSE;

  $htmlOld = padApp . "_regression/$item.html";
  $htmlNew = padApp . "$item.html";

  $old = padFileGetContents ( $htmlOld );

  $new = getPage ($item);
  $new = $new ['data'];

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

    $demoMode = FALSE;
    if ( count($oldRes) and count ($oldRes) == count($newRes) and count($oldRes) == count($newSrc)) 
     foreach ( $oldRes as $key => $value ) 
        if ( $oldRes [$key] <> $newRes [$key])
          $demoMode = TRUE;

    if ( $demoMode) {

      $demoModeLines = [];

      foreach ( $oldRes as $key => $value ) {
        $demoModeLines [$key] ['newSrc'] = str_replace ('}', '&close;', padColorsString ( $newSrc [$key] ) );
        $demoModeLines [$key] ['oldRes'] = $oldRes [$key];
        $demoModeLines [$key] ['newRes'] = $newRes [$key];
      }

    }

  }

  $onlyResult = onlyResult ($htmlNew);

?>