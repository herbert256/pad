<?php

  // The console writer's error report: the page that fails is answered with the message
  // whole - it was cut at 100 characters, which on the engine's own file path and line
  // left 'there is no page named ...' without the name of the page. Fetched as a browser
  // would: a curl caller is answered by the JSON channel instead (error/claude.php).

  $r = padCurl ( [ 'url'     => $padHost . 'regression/output_console/?broken&padInclude',
                   'options' => [ 'USERAGENT' => 'Mozilla/5.0 pad' ] ] );

  $whole = ( $r ['result'] == '500'
             and str_contains ( $r ['data'], "there is no page named 'no/such/page/with/a/name/long/enough/to/pass/the/cut' in the application 'regression/output_console'" ) ) ? 'yes' : 'NO';

  // A visitor from elsewhere - a forwarded request is somebody else's (error/claude.php) -
  // gets the request id and nothing more, as under every other output type: the console
  // report went to anyone, the server's paths and the template's source with it.

  $far = padCurl ( [ 'url'     => $padHost . 'regression/output_console/?broken&padInclude',
                     'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ],
                     'options' => [ 'USERAGENT' => 'Mozilla/5.0 pad' ] ] );

  $hidden = ( $far ['result'] == '500'
              and preg_match ( '/^Error: [A-Za-z0-9]{8}$/', trim ( $far ['data'] ) ) ) ? 'yes' : 'NO';

?>
