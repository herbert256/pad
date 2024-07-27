<?php

  include_once pad . 'info/types/xref/_lib.php';
    
  $padXrefId     = 0;
  $padXrefDepth  = 0;
  $padXrefEvents = [];
  $padXrefSource = padInfoGet ( padApp . $padStartPage . '.pad' );

  if ( $padXrefXml and file_exists ( padData . padXrefLocation () . "xref.xml" ) )
    unlink ( padData . padXrefLocation () . "xref.xml" );

?>