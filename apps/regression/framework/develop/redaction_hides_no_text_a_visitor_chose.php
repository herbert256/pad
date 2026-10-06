<?php

  // The reports redact the request's own random secrets wherever they stand - but the
  // session cookies are the visitor's to send: a PHPSESSID or padSesID chosen to read like
  // the payload of an attack made that payload disappear from every report of it, the
  // dumps, the JSON channel, track. Only what the server minted is redacted by its value.

  $_COOKIE [ session_name () ] = 'UNION SELECT password';
  $_COOKIE ['padSesID']        = 'evilload';
  $padSesID                    = 'evilload';

  $token  = padCsrfToken ();
  $shown  = padRedact ( [ "1' UNION SELECT password FROM users", 'see evilload here', "token $token" ] );
  $result = implode ( ' | ', $shown );

?>
