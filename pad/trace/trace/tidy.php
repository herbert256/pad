<?php
 
  $tidyoptions = [
    'input-xml'           => true,
    'output-xml'          => true,
    'force-output'        => true,
    'add-xml-decl'        => FALSE,
    'indent'              => TRUE,
    'tab-size'            => 2,
    'indent-spaces'       => 2,
    'vertical-space'      => 'no',
    'wrap'                => 0,
    'clean'               => 'yes',
    'drop-empty-elements' => 'yes'
  ];

  $data = padFileGetContents ( padData . $padTraceXmlFile );

  $xml = new tidy;
  $xml->parseString($data, $tidyoptions, 'utf8');
  $xml->cleanRepair();

  if ( $xml === FALSE )
    return padError ( "TIDY conversion error");

  $data = trim($xml->value);

  padFilePutContents ( $padTraceXmlFile, $data );

?>