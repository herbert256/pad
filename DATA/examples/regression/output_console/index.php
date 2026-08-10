<?php

  // The console writer's promise: the page echoed straight to the client, nothing but the
  // body - and none of the headers the web writer decorates a response with, which is
  // what separates it from web. padCurl trims what it fetches, so the closing newline the
  // writer adds cannot be asserted here; the exact body can.

  $r = padCurl ( $padHost . 'regression/output_console/?payload&padInclude' );

  $verdict = ( $r ['result'] == '200'
               and ! isset ( $r ['headers'] ['PAD'] )
               and $r ['data'] == '<p>CARRIED ALL THE WAY</p>' ) ? 'yes' : 'NO';

  $output = 'console';

?>