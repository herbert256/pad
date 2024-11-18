<?php

  include_once PAD . 'info/types/xref/_lib.php';
    
  $padInfoXrefId     = 0;
  $padInfoXrefDepth  = 0;
  $padInfoXrefEvents = [];
  $padInfoXrefSource = padInfoGet ( APP . $padStartPage . '.pad' );

  if ( $padInfoXrefXml and file_exists ( DAT . padInfoXrefLocation () . "xref.xml" ) )
    unlink ( DAT . padInfoXrefLocation () . "xref.xml" );

?>