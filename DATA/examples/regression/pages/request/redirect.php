<?php

  // The hop fixture answers with a redirect. Unfollowed, the redirect itself shows -
  // status and destination; followed, the request lands on the vars fixture.

  $stay = padCurl ( [ 'url' => $padGoExt . 'request/hop&padInclude',
                      'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $follow = padCurl ( $padGoExt . 'request/hop&padInclude' );

  // The redirect target is a plain page name, so the follow lands on the full page,
  // wrapper and all - the assertion is that the fixture's line is in it.

  $stayResult   = $stay ['result'] . ' lands on vars: ' . ( str_contains ( $stay ['info'] ['redirect_url'] ?? '', 'request/vars' ) ? 'yes' : 'no' );
  $followResult = $follow ['result'] . ' contains vars: ' . ( str_contains ( $follow ['data'], 'a is unset and b is unset' ) ? 'yes' : 'no' );

?>