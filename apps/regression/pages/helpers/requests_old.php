<?php

  // The redirect after a failed post: the post is answered with a redirect back to the
  // page it came from (its Referer), and the input is flashed - the form page asked with the
  // cookies the redirect set, as a browser would, has the values; asked again it has none.

  $oldPost = padCurl ( [
    'url'     => $padGoExt . 'helpers/requests_old_post',
    'post'    => 'email=+ann@example.com+&password=secret&address[city]=Delft',
    'options' => [ 'FOLLOWLOCATION' => FALSE, 'REFERER' => $padGoExt . 'helpers/requests_old_form&x=1' ]
  ] );

  $oldJar  = [ 'PHPSESSID' => $oldPost ['cookies'] ['PHPSESSID'] ?? '', 'padOld' => $oldPost ['cookies'] ['padOld'] ?? '' ];
  $oldTo   = $oldPost ['headers'] ['Location'] ?? '';
  $oldOne  = padCurl ( [ 'url' => "$oldTo&padInclude", 'cookies' => $oldJar ] );
  $oldTwo  = padCurl ( [ 'url' => "$oldTo&padInclude", 'cookies' => $oldJar ] );

  $oldResult = $oldPost ['result'] . ' ' . str_replace ( $padGoExt, '', $oldTo ) . ' sign: ' . ( $oldJar ['padOld'] ?: 'none' )
             . ' | ' . trim ( $oldOne ['data'] )
             . ' sign: ' . ( ( $oldOne ['cookies'] ['padOld'] ?? '' ) === 'deleted' ? 'taken' : 'kept' )
             . ' | ' . trim ( $oldTwo ['data'] );

?>
