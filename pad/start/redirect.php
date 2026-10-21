<?php

  // Body of the {redirect} tag: sends a Location header to $padParm and ends the request.
  // Every variable {set} at this level is passed along as a query parameter. padRedirect()
  // does not return, so nothing after this line runs.

  // The target is an address, no HTML: a field inside the quoted target - {redirect
  // '?search&q={$q}'} - arrives escaped by its sanitize step and with the stand-ins of its
  // , = | @ ' " \, and the {?q} query fragment with its = as a stand-in; both went into the
  // Location header as they were, q=Tom &amp; Jerry cut q at the &, and &q\u{E03D}Tom+%26+Jerry
  // was one parameter without a value. padParmText gives the text the target holds
  // (lib/encoding.php), and a line break an entity decodes to - &#10; - is no part of an
  // address: it is taken out, so it cannot start a header of its own.
  //
  // Whether the target leaves the site is read from the text as it came, before the decode:
  // a visitor's &#104;ttps://elsewhere passes the sanitize step as it is, and decoded first it
  // would have become an address off the site where it was a page of this one.

  $padRedirectAway = preg_match ( '#^https?://#i', (string) $padParm );

  $padParm = preg_replace ( '/[\r\n\0]+/', '', padParmText ( $padParm ) );

  // A redirect to a page that is not there is a clean 302 into a 404. When the target is
  // a bare page name of this application, strict mode checks it now, as the router will
  // resolve it (lib/page.php); a URL-shaped target leaves the application and is its own
  // business.

  if ( $padCheckSyntax
       and ! str_contains ( $padParm, '://' )
       and ! str_contains ( $padParm, '&' )
       and ! str_starts_with ( $padParm, '?' )
       and ! str_starts_with ( $padParm, '/' )
       and ! padPageCheck ( strtok ( $padParm, '#' ) ) )
    padError ( "there is no page named '" . strtok ( $padParm, '#' ) . "' for {redirect}" );

  // An absolute address - http:// or https:// - is where the browser goes, as written: the
  // tag's target is a URL (TAGS.md) and the check above lets one leave the application. It
  // went through padRedirect as a page name and came out as ?https://... on this site. The
  // values set on the tag ride along; the visitor's ids do not leave the site. padRedirect
  // itself stays inside the site, so a PHP page handing it a value from the request is no
  // open redirect - a template that writes {redirect $url} decides where that leads.

  if ( $padRedirectAway and preg_match ( '#^https?://#i', $padParm ) ) {

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
