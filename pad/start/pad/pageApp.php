<?php

  $padPagePage    = $padParm;
  $padPageApp     = padTagParm ( 'app' );
  $padPageInclude = padTagParm ( 'include', TRUE );
  $padPageAjax    = padTagParm ( 'ajax',    TRUE );

  $padPageInclude = ( $padPageInclude ) ? '&padInclude' : '';

  if ( $padPageAjax )
    return padPageAjax ( $padPagePage, $padPageInclude, $padPageApp );

  $padPageUrl  = "$padHost/$padPageApp/?$padPagePage$padPageInclude";  
  $padPageCurl = padCurl ( $padPageUrl );

  if ( ! str_starts_with ( $padPageCurl ['result'], '2' ) )
    return FALSE;
  
  return $padPageCurl ['data'] ?? '';

?>