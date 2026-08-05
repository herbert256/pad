<?php

  // Treats $data as a URL, fetches it, and feeds the response body back through padData()
  // using the content type curl reported, so a remote JSON/XML/CSV document becomes a PAD
  // data array. Included by padData() as data/<type>.php; padContentType picks 'curl' for
  // anything starting http: or https:. A non-2xx status is a padError.

  $curl = padCurl ($data);

  if ( str_starts_with ( $curl ['result'],  '2' ) )
    return padData ( $curl ['data'], $curl ['type'], $name );
  else
    return padError ( "Curl failed: " . $curl ['result'] );

?>