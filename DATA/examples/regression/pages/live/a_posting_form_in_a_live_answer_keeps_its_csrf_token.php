<?php

  // A posting form inside a live region on a page with $padCsrf on carries the session's
  // token in the page, and in the answer to an event, which the script puts in the region in
  // its place: the answer was the region as rendered, before the forms got their tokens, and
  // every post of the form after the first event was refused 403.

  $liveUrl  = $padHost . 'regression/pages/?live/form&padInclude';
  $livePage = padCurl ( $liveUrl );
  $liveJar  = [ 'PHPSESSID' => $livePage ['cookies'] ['PHPSESSID'] ?? '' ];
  $liveCsrf = preg_match ( '/data-pad-csrf="([0-9a-f]{64})"/', $livePage ['data'], $liveMatch ) ? $liveMatch [1] : '';
  $liveTag  = 'name="padCsrfToken" value="' . $liveCsrf . '"';

  $liveEvent = padCurl ( [ 'url' => $liveUrl, 'cookies' => $liveJar,
                           'post' => [ 'padLive' => 'box', 'padEvent' => 'inc', 'padCsrfToken' => $liveCsrf ] ] );

  $liveFormResult = 'the form in the page carries the token: ' . ( ( $liveCsrf and str_contains ( $livePage ['data'], $liveTag ) ) ? 'yes' : 'no' )
                  . ', the answer: ' . $liveEvent ['result'] . ' ' . ( str_contains ( $liveEvent ['data'], '<p>2</p>' ) ? 'rendered' : 'not rendered' )
                  . ', the form in the answer carries the token: ' . ( ( $liveCsrf and str_contains ( $liveEvent ['data'], $liveTag ) ) ? 'yes' : 'no' );

?>
