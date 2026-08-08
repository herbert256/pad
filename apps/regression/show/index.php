<?php

  // Fetched the way the crawl fetches: a root index renders full, everything else bare -
  // one rule, or an unchanged index shows a phantom diff here, which is what happened when
  // the crawl learnt the full-index rule and this page did not (the audit's F-05).

  $include = ( $item != 'index' ) ? '&padInclude' : '';

  $curl = padCurl ( "$padHost$app/?$item$include" );

  // A page that answers with an error stays on this page: what it answered is the evidence
  // the comparison needs, where the redirect this used to make just abandoned the visitor
  // on the failing page itself.

  $oldRes = $newRes = $newSrc = $compare = $demoLines = [];

  $title = $item;
  $new   = $curl ['data'];
  $old   = padFileGet ( DATA . "regression/$app/$item.html" );
  $diff  = diff ( $old, $new );

  $check = $old;
  while ( strpos($check, '<!-- START DEMO RESULT -->') )
    $oldRes [] = trim ( cut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

  $check = $new;
  while ( strpos($check, '<!-- START DEMO RESULT -->') )
    $newRes [] = trim ( cut ( $check, '<!-- START DEMO RESULT -->', '<!-- END DEMO RESULT -->' ) );

  $check = $new;
  while ( strpos($check, '<!-- START DEMO SOURCE -->') )
    $newSrc [] = trim ( cut ( $check, '<!-- START DEMO SOURCE -->', '<!-- END DEMO SOURCE -->' ) );

  foreach ( $oldRes as $key => $value )

    if ( isset ($newRes [$key]) and $oldRes [$key] != $newRes [$key] ) {
      $compare   [$key] ['diff']   = diff ( $oldRes [$key], $newRes [$key] );
      $demoLines [$key] ['newSrc'] = $newSrc [$key];
      $demoLines [$key] ['oldRes'] = $oldRes [$key];
      $demoLines [$key] ['newRes'] = $newRes [$key];
    }

?>