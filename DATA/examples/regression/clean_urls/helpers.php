<?php

  // The request helpers in an application of clean links with a session variable. padUrl
  // writes the clean form here, as $padGo does. And a value padSessionPut keeps under the
  // $padSessionVars name member is the one the end of the request writes back - the
  // variable follows the session, or the old value of $member would be written back over
  // it - so the next request finds it; padSessionForget takes it out the same way. The
  // members/[member] route shows the session's member.

  $helpersLinks = implode ( ' ', [ padUrl ( 'products/42' ), padUrl ( 'products/7', [ 'tab' => 'reviews' ] ), padUrl ( 'about&x=1' ) ] );

  $helpersBase = $padHost . 'regression/clean_urls/index.php/';
  $helpersPut  = padCurl ( $helpersBase . 'helpers_put?padInclude' );
  $helpersJar  = [ 'PHPSESSID' => $helpersPut ['cookies'] ['PHPSESSID'] ?? '' ];
  $helpersAsk  = fn ( $page ) => trim ( padCurl ( [ 'url' => $helpersBase . "$page?padInclude", 'cookies' => $helpersJar ] ) ['data'] );

  $helpersSession = implode ( ' | ', [ trim ( $helpersPut ['data'] ), $helpersAsk ( 'members/eve' ),
                                       $helpersAsk ( 'helpers_forget' ), $helpersAsk ( 'members/eve' ) ] );

?>
