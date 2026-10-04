<?php

  // Treats $data as a URL, fetches it, and feeds the response body back through padData()
  // using the content type curl reported, so a remote JSON/XML/CSV document becomes a PAD
  // data array. Included by padData() as data/<type>.php; padContentType picks 'curl' for
  // anything starting http: or https:. A non-2xx status is a padError.
  //
  // A fetched body is data. PAD's own list syntax - ( 'a', $b, 2 * 3 ) - evaluates every
  // element as an expression, so under $padProtectValues a body that reads as one is
  // refused rather than run.

  $curl = padCurl ($data);

  if ( ! str_starts_with ( $curl ['result'],  '2' ) )
    return padError ( "Curl failed: " . $curl ['result'] );

  if ( $GLOBALS ['padProtectValues'] and is_string ( $curl ['data'] ) ) {

    $curlBody = $curl ['data'];

    if ( ( $curl ['type'] ?: padContentType ( $curlBody ) ) == 'list' )
      return padError ( "the document fetched from $data reads as a PAD list, whose elements would run as expressions" );

  }

  return padData ( $curl ['data'], $curl ['type'], $name );

?>