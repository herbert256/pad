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

  if ( $padPageAjax )
    return padPageAjax ( $padPagePage, $padPageInclude, $padPageApp );

  $padPageUrl  = "$padHost$padPageApp/?$padPagePage$padPageInclude";
  $padPageCurl = padCurl ( $padPageUrl );

  if ( ! str_starts_with ( $padPageCurl ['result'], '2' ) )
    return FALSE;
  
  return $padPageCurl ['data'] ?? '';

?>