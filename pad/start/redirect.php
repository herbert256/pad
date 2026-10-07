<?php

  // Body of the {redirect} tag: sends a Location header to $padParm and ends the request.
  // Every variable {set} at this level is passed along as a query parameter. padRedirect()
  // does not return, so nothing after this line runs.

  // A redirect to a page that is not there is a clean 302 into a 404. When the target is
  // a bare page name of this application, strict mode checks it now, as the router will
  // resolve it (lib/page.php); a URL-shaped target leaves the application and is its own
  // business.

  if ( $padCheckSyntax
       and ! str_contains ( $padParm, '://' )
       and ! str_contains ( $padParm, '&' )
       and ! str_starts_with ( $padParm, '?' )
       and ! str_starts_with ( $padParm, '/' )
       and ! padPageCheck ( $padParm ) )
    padError ( "there is no page named '$padParm' for {redirect}" );

  // An absolute address - http:// or https:// - is where the browser goes, as written: the
  // tag's target is a URL (TAGS.md) and the check above lets one leave the application. It
  // went through padRedirect as a page name and came out as ?https://... on this site. The
  // values set on the tag ride along; the visitor's ids do not leave the site. padRedirect
  // itself stays inside the site, so a PHP page handing it a value from the request is no
  // open redirect - a template that writes {redirect $url} decides where that leads.

  if ( preg_match ( '#^https?://#i', (string) $padParm ) ) {

    // The values go into the query, in front of a #fragment: behind it they were part of
    // the fragment, which the browser keeps to itself.

    list ( $padRedirectUrl, $padRedirectHash ) = array_pad ( explode ( '#', (string) $padParm, 2 ), 2, NULL );

    foreach ( $padSetLvl [$pad] as $padK => $padV )
      $padRedirectUrl = padAddGet ( $padRedirectUrl, $padK, $padV );

    if ( $padRedirectHash !== NULL )
      $padRedirectUrl .= "#$padRedirectHash";

    padHeader ( "Location: $padRedirectUrl" );
    padExit ( 302 );

  }

  padRedirect ( $padParm, $padSetLvl [$pad] );

?>
