<?php

  // Body of the {ajax} tag: instead of rendering another page inline, emit a <div> plus the
  // XMLHttpRequest that fills it once the browser has the response.
  //
  // $padParm names the page, the app= parameter picks a different application, and every
  // variable {set} at this level ($padSetLvl[$pad]) is appended to the query string along
  // with padInclude, so the fetched page renders bare, without its _inits/_exits wrappers.
  // Returns the markup built by padPageAjax as the tag's value.

  $padExtPag = $padParm ;
  $padExtApp = padTagParm ( 'app' );
  $padExtQry = '&padInclude';

  // The stub renders happily today and the visitor's browser meets the 404 later. The
  // page is checkable now, in this application or the named one, as the router of that
  // application will resolve it (lib/page.php); the lenient walk keeps the stub as it
  // always was.

  if ( $padCheckSyntax ) {

    if ( $padExtApp ) {

      $padExtOk = ( preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_\/-]*$/D', $padExtApp )
                    and ! str_contains ( $padExtApp, '//' )
                    and padPageCheck ( $padExtPag, APPS . "$padExtApp/" ) );

      if ( ! $padExtOk )
        padError ( "there is no page named '$padExtPag' in the application '$padExtApp'" );

    } elseif ( ! padPageCheck ( $padExtPag ) )

      padError ( "there is no page named '$padExtPag' for {ajax}" );

  }

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  return padPageAjax ( $padExtPag, $padExtQry, $padExtApp ) ;

?>