<?php

  // A PAD page fetching another shares its session by design: padCurl adds the caller's
  // padSesID cookie to any own-host fetch. The fixture prints the id it was given, and it
  // must be this very request's.

  $curl = padCurl ( $padGoExt . 'request/ses&padInclude' );

  $sessionResult = $curl ['result'] . ' shared: '
                 . ( trim ( $curl ['data'] ) === "sid:$padSesID" ? 'yes' : 'no' );

?>