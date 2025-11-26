<?php

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

  $arr = include 'data/xml.php';

  return $arr;

?>