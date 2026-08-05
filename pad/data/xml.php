<?php

  // Reads $data as XML and returns it as a PAD data array. Included by padData() as
  // data/<type>.php, and directly by data/html.php once tidy has cleaned the markup up.
  // The conversion itself lives in data/_lib/xml.php, which is loaded on demand rather
  // than at start-up because most requests never touch XML.

  include_once PAD . 'data/_lib/xml.php';

  return padXmlToArray ( $data );

?>