<?php

  include_once '/pad/info/types/xref/_lib.php';
    
  $padInfXrefId     = 0;
  $padInfXrefDepth  = 0;
  $padInfXrefEvents = [];
  $padInfXrefSource = padInfoGet ( '/app/' . $padStartPage . '.pad' );

  if ( $padInfXrefXml and file_exists ( '/data/' . padXrefLocation () . "xref.xml" ) )
    unlink ( '/data/' . padXrefLocation () . "xref.xml" );

?>