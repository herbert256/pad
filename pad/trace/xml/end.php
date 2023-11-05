<?php
 
  $tidyoptions = [
    'input-xml'           => true,
    'output-xml'          => true,
    'force-output'        => true,
    'indent'              => TRUE,
    'add-xml-decl'        => true,
    'tab-size'            => 2,
    'vertical-space'      => 'yes',
    'clean'               => 'yes',
    'drop-empty-elements' => 'yes'
  ];

  $data = padFileGetContents ( padData . "$padTraceBase/trace.xml" );

  $xml = new tidy;
  $xml->parseString($data, $tidyoptions, 'utf8');
  $xml->cleanRepair();

  if ( $xml === FALSE )
    return padError ( "TIDY conversion error");

  $data = trim($xml->value);

  padFilePutContents ( "$padTraceBase/tidy.xml", $data);

?>