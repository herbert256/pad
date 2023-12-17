<?php

  include_once pad . 'info/types/xref/lib.php';
    
  $padXrefDepth  = 0;
  $padXrefEvents = [];
  $padXrefSource = padInfoGet ( padApp . $padStartPage . '.pad' );
  $padXrefFile   = "$padInfoDir/xref.xml";

?>