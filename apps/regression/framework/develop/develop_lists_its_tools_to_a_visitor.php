<?php

  // The index only lists the tools - it acts on nothing, and the static copy of the site
  // (pages.sh) carries it - so a visitor still gets it, while every tool turns them away.

  $curl   = padCurl ( [ 'url' => $padHost . 'develop/?index&padInclude', 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
  $answer = $curl ['result'] . ': ' . ( str_contains ( $curl ['data'], '?clean&amp;go=1' ) || str_contains ( $curl ['data'], '?clean&go=1' ) ? 'the list' : 'no list' );

?>
