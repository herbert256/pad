<?php
 
  global $padXmlFile;
  
  padXmlWriteClose ( 'pad' );

  $padXmlTidyOptions = [
    'input-xml'           => true,
    'output-xml'          => true,
    'force-output'        => true,
    'add-xml-decl'        => false,
    'indent'              => true,
    'tab-size'            => 2,
    'indent-spaces'       => 2,
    'vertical-space'      => 'no',
    'wrap'                => 0,
    'clean'               => 'yes',
    'drop-empty-elements' => 'yes'
  ];

  $padXmlTidy = new tidy;
  $padXmlTidy->parseString(padFileGetContents (padData.$padXmlFile), $padXmlTidyOptions, 'utf8');
  $padXmlTidy->cleanRepair();

  if ( $padXmlTidy === FALSE )
    return padError ( "TIDY conversion error");

  padFilePutContents ( $padXmlFile, $padXmlTidy->value );

?>