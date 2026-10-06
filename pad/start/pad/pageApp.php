<?php

  // {page ... app='other'}: the page lives in another application, which has its own
  // config, _lib and database, so it cannot be built in this process and is reached over
  // HTTP instead. Called from start/page.php when the tag carries an app= parameter.
  //
  // ajax= (default TRUE) leaves the fetching to the browser and returns the div plus
  // XMLHttpRequest built by padPageAjax. Otherwise padCurl fetches the page here and now
  // and its body becomes the tag's value, with a non-2xx status returning FALSE.
  // include= (default TRUE) asks the other app to render the page without its wrappers.

  $padPagePage    = $padParm;
  $padPageApp     = padTagParm ( 'app' );
  $padPageInclude = padTagParm ( 'include', TRUE );
  $padPageAjax    = padTagParm ( 'ajax',    TRUE );

  $padPageInclude = ( $padPageInclude ) ? '&padInclude' : '';

  // The application and the page are checked as that application's router will resolve
  // them (lib/page.php), as {ajax app=} checks them under the strict check. Neither was,
  // and both went as they came into the script the browser runs and into the address this
  // server fetches for itself: {page $p, app='shop'} with p=x");alert(1);// ran a script. A
  // page that is not there is an error under the strict check and nothing without it, as
  // for a {page} of this application.

  if ( ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_\/-]*$/D', (string) $padPageApp ) or str_contains ( $padPageApp, '//' )
       or ! padPageCheck ( (string) $padPagePage, APPS . "$padPageApp/" ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no page named '$padPagePage' in the application '$padPageApp'" );

    return '';

  }

  if ( $padPageAjax )
    return padPageAjax ( $padPagePage, $padPageInclude, $padPageApp );

  // A route segment may hold what a query string reads as its own - products/[id] takes
  // 1&padStats - so each segment is encoded, and the other application binds the value
  // whole instead of taking a switch from it.

  $padPageUrl  = "$padHost$padPageApp/?" . implode ( '/', array_map ( 'rawurlencode', explode ( '/', (string) $padPagePage ) ) ) . $padPageInclude;
  $padPageCurl = padCurl ( $padPageUrl );

  if ( ! str_starts_with ( $padPageCurl ['result'], '2' ) )
    return FALSE;

  // The other application's finished page, as it was written out: any brace in it is a
  // real one again. Re-scanned here it would run, so level/go.php protects it under
  // $padProtectValues like any tag's answer; this returns the body as it came.

  return $padPageCurl ['data'] ?? '';

?>
