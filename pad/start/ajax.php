<?php

  // Body of the {ajax} tag: instead of rendering another page inline, emit a <div> plus the
  // XMLHttpRequest that fills it once the browser has the response.
  //
  // $padParm names the page, the app= parameter picks a different application, and every
  // variable {set} at this level ($padSetLvl[$pad]) is appended to the query string along
  // with padInclude, so the fetched page renders bare, without its _inits/_exits wrappers.
  // Returns the markup built by padPageAjax as the tag's value.

  $padExtPag = $padParm ;
  $padExtApp = padTagParm ( 'app' );
  $padExtQry = '&padInclude';

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  return padPageAjax ( $padExtPag, $padExtQry, $padExtApp ) ;

?>