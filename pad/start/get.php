<?php

  // Body of the {get} tag: fetches another page of this same application over HTTP with curl
  // and returns its output as the tag's value - a real second request, not a nested pass.
  //
  // $padParm names the page and every variable {set} at this level ($padSetLvl[$pad]) is
  // appended to the query string, together with padInclude so the page renders bare, without
  // its _inits/_exits wrappers. The result is padEscape()d so that braces, pipes and @ signs
  // coming back from the other page are not parsed as PAD markup here.

  $padExtPag = $padParm ;
  $padExtQry = '&padInclude';

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  return padEscape ( padPageGet ( $padExtPag, $padExtQry ) );

?>