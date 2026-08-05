<?php

  // Web writer: sends the page to the browser through padWebSend, which handles the
  // headers and the gzip decision.
  //
  // Turns a 200 into a 304 first when $padWebEtag304 is on and the client already holds
  // the ETag we were about to send.

  if ( $padStop == '200' and $padWebEtag304 and ( $padClientEtag ?? '' ) == $padEtag )
    $padStop = 304;

  padWebSend ( $padStop );

?>