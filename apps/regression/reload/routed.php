<?php

  // The poll of a page a route reached is the address the page was asked at - item/a#b&c,
  // the id decoded - written into the script as it stood: the browser took #b&c&padReload
  // for a fragment and asked for item/a, which answered a whole page instead of the stamp,
  // so the page never reloaded. The poll is asked the way the browser asks it.

  $routedPage = padCurl ( $padHost . 'regression/reload/?item/a%23b%26c' ) ['data'] ?? '';
  $routedAt   = preg_match ( '/var url = ("[^"]*")/', $routedPage, $routedMatch ) ? (string) json_decode ( $routedMatch [1] ) : '';
  $routedPoll = $routedAt !== '' ? padCurl ( substr ( $padHost, 0, -strlen ( $padRoot ) ) . explode ( '#', $routedAt ) [0] ) : [];

  $routedVerdict = ( str_contains ( $routedPage, 'item a#b&amp;c' ) and ( $routedPoll ['result'] ?? '' ) == 200
                     and ctype_digit ( trim ( $routedPoll ['data'] ?? '' ) ) ) ? 'yes' : 'NO';

?>
