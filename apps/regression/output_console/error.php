<?php

  // The console writer's error report: the page that fails is answered with the message
  // whole - it was cut at 100 characters, which on the engine's own file path and line
  // left 'there is no page named ...' without the name of the page. Fetched as a browser
  // would: a curl caller is answered by the JSON channel instead (error/claude.php).

  $r = padCurl ( [ 'url'     => $padHost . 'regression/output_console/?broken&padInclude',
                   'options' => [ 'USERAGENT' => 'Mozilla/5.0 pad' ] ] );

  $whole = ( $r ['result'] == '500'
             and str_contains ( $r ['data'], "there is no page named 'no/such/page/with/a/name/long/enough/to/pass/the/cut' in the application 'regression/output_console'" ) ) ? 'yes' : 'NO';

?>
