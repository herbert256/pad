<?php

  // The region, the event and the value of a live post are texts, and a post that sends
  // one of them as a list - padLive[]=box, padEvent[]=go, padValue[]=7 - is a post without
  // it: a list was read as a text, and the Array to string conversion ended the request -
  // of any page, since every request asks whether it is a live one.

  $boxUrl    = $padGoExt . 'request/live_names_are_texts&padInclude';
  $boxResult = [];

  foreach ( [ 'padLive=box&padEvent=go&padValue=7',
              'padLive=box&padEvent=go&padValue%5B%5D=7',
              'padLive=box&padEvent%5B%5D=go&padValue=7',
              'padLive%5B%5D=box&padEvent=go&padValue=7' ] as $boxPost ) {

    $boxCurl = padCurl ( [ 'url' => $boxUrl, 'post' => $boxPost ] );
    $boxData = trim ( $boxCurl ['data'] );

    $boxResult [] = urldecode ( $boxPost ) . ': ' . $boxCurl ['result'] . ' '
                  . ( str_starts_with ( $boxData, '<p>' ) ? $boxData : ( str_contains ( $boxData, 'data-pad-live="box"' ) ? 'the page' : 'something else' ) );

  }

  $boxResult = implode ( "\n", $boxResult );

?>
