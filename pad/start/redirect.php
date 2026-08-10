<?php

  // Body of the {redirect} tag: sends a Location header to $padParm and ends the request.
  // Every variable {set} at this level is passed along as a query parameter. padRedirect()
  // does not return, so nothing after this line runs.

  // A redirect to a page that is not there is a clean 302 into a 404. When the target is
  // a bare page name of this application, strict mode checks it now; a URL-shaped target
  // leaves the application and is its own business.

  if ( $padCheckSyntax
       and ! str_contains ( $padParm, '://' )
       and ! str_contains ( $padParm, '&' )
       and ! str_starts_with ( $padParm, '?' )
       and ! str_starts_with ( $padParm, '/' )
       and ! padAppPageCheck ( $padParm ) )
    return padError ( "there is no page named '$padParm' for {redirect}" );

  padRedirect ( $padParm, $padSetLvl [$pad] );

?>