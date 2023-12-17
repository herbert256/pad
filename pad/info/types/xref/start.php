<?php

  include_once pad . 'info/types/xref/lib.php';
    
  $padXrefDepth      = 0;
  $padXrefXmlEvents  = [];
  $padXrefPageSource = padInfoGet ( padApp . $padStartPage . '.pad' );

  $padXrefFile = "$padInfoPage/xref.xml";

  if ( file_exists ( $padXrefFile ) )
    unlink ( $padXrefFile );

?>