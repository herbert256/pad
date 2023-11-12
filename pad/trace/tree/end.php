<?php
 
  padTreePrint ();

  global $padTreeFile;

  $padTreeTidyOptions = [
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

  $padTreeTidy = new tidy;
  $padTreeTidy->parseString(padFileGetContents ( padData . "$padTreeFile/tree.xml" ), $padTreeTidyOptions, 'utf8');
  $padTreeTidy->cleanRepair();

  if ( $padTreeTidy === FALSE )
    return padError ( "TIDY conversion error");

  padFilePutContents ( "$padTreeFile/tree.xml" , $padTreeTidy->value );

?>