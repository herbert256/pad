<?php

  // field_superglobals asked for with a cookie, a header and an engine name in the query:
  // none of them is a variable of the page, so none of them is a field of its template -
  // not through the search of the global arrays, not as a prefix.

  $curl = padCurl ( [ 'url'     => $padGoExt . 'request/field_superglobals&padInclude&padZzField=1',
                      'cookies' => [ 'crumbField' => 'baked' ],
                      'headers' => [ 'X-Zz-Field' => 'sent' ] ] );

  $fieldSent = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>
{!fieldSent}
