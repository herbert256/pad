<?php

  // The web writer's promise: a plain 200, an html content type, the body shipped as the
  // page - and no attachment headers, which is what separates it from download.

  $r = padCurl ( $padHost . 'regression/output_web/?payload&padInclude' );

  $verdict = ( $r ['result'] == '200'
               and str_contains ( $r ['headers'] ['Content-Type'] ?? '', 'text/html' )
               and ! isset ( $r ['headers'] ['Content-Disposition'] )
               and str_contains ( $r ['data'], 'CARRIED ALL THE WAY' ) ) ? 'yes' : 'NO';

  // A page of another content type is shipped as it was built: tidy, which knows only
  // HTML, used to wrap the JSON in <html><head><body>.

  $j = padCurl ( $padHost . 'regression/output_web/?json' );

  $verdictJson = ( $j ['result'] == '200'
                   and str_contains ( $j ['headers'] ['Content-Type'] ?? '', 'application/json' )
                   and $j ['data'] === '{"web":true}' ) ? 'yes' : 'NO';

  $output = 'web';

?>
