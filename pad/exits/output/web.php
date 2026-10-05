<?php

  // Web writer: sends the page to the browser through padWebSend, which handles the
  // headers and the gzip decision.
  //
  // Turns a 200 into a 304 first when $padWebEtag304 is on and the client already holds
  // the ETag we were about to send - one of the tags of its If-None-Match, or *.

  // The client's If-None-Match is the list inits/client.php read: any tag of it, or *, is a
  // match - as the cache's own path has it. Only the first was compared, so a browser
  // holding two versions, or asking with *, was sent the whole page again.

  $padWebTags = $padClientEtags ?? [];

  if ( $padStop == '200' and $padWebEtag304 and $padEtag !== ''
       and ( in_array ( '*', $padWebTags, TRUE ) or in_array ( $padEtag, $padWebTags, TRUE ) ) )
    $padStop = 304;

  padWebSend ( $padStop );

?>