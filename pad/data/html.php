<?php

  // Reads $data as (possibly malformed) HTML and returns it as a PAD data array. Runs the
  // input through tidy to get well-formed XML out of it, then hands the repaired markup to
  // data/xml.php. Included by padData() as data/<type>.php; padContentType picks 'html'
  // for documents opening with <html.

  $tidyoptions = [
    'output-xml'   => true,
    'force-output' => true
  ];

  $xml = new tidy;
  $xml->parseString($data, $tidyoptions, 'utf8');
  $xml->cleanRepair();

  if ( $xml === FALSE )
    return padError ( "TIDY conversion error");

  $data = trim($xml->value);

  $arr = include PAD . 'data/xml.php';

  return $arr;

?>