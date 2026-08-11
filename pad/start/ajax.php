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
  // page is checkable now, in this application or the named one; the lenient walk keeps
  // the stub as it always was.

  if ( $padCheckSyntax ) {

    if ( $padExtApp ) {

      $padExtOk = FALSE;

      foreach ( [ 'pad', 'php', 'html' ] as $padExtExt )
        if ( file_exists ( APPS . "$padExtApp/$padExtPag.$padExtExt" ) )
          $padExtOk = TRUE;

      if ( ! $padExtOk )
        padError ( "there is no page named '$padExtPag' in the application '$padExtApp'" );

    } elseif ( ! padAppPageCheck ( $padExtPag ) )

      padError ( "there is no page named '$padExtPag' for {ajax}" );

  }

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

  return padPageAjax ( $padExtPag, $padExtQry, $padExtApp ) ;

?>