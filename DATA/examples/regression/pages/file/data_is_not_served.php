<?php

  // DATA/ holds what the engine and the applications keep at run time - sessions, the keys
  // padEncrypt uses, the console's users, logs, error dumps - and the web server never
  // serves it: a file just written there is not to be had over HTTP. A www/DATA link to it
  // once put all of it on the network, and a .php file under it ran.

  $probe = 'temp/data_is_not_served.txt';

  padFilePut ( $probe, 'kept in DATA' );

  $served = padCurl ( $padHost . 'DATA/' . $probe );

  @unlink ( DATA . $probe );

  $answer = ( str_starts_with ( (string) $served ['result'], '2' ) and str_contains ( (string) ( $served ['data'] ?? '' ), 'kept in DATA' ) )
          ? 'served' : 'not served';

?>
