<?php

  include_once '/pad/info/types/xref/_lib.php';
    
  $padInfoXrefId     = 0;
  $padInfoXrefDepth  = 0;
  $padInfoXrefEvents = [];
  $padInfoXrefSource = padInfoGet ( '/app/' . $padStartPage . '.pad' );

  if ( $padInfoXrefXml and file_exists ( '/data/' . padInfoXrefLocation () . "xref.xml" ) )
    unlink ( '/data/' . padInfoXrefLocation () . "xref.xml" );

?>