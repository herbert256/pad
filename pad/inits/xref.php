<?php

  $padXref = FALSE;

  if ( $padXrefManual  ) $padXref = TRUE;
  if ( $padXrefDevelop ) $padXref = TRUE;
  if ( $padXrefReverse ) $padXref = TRUE;

  if ( $padXml   and $padXmlXref   ) $padXref = TRUE;
  if ( $padTrace and $padTraceXref ) $padXref = TRUE;

  if ( $padXref )
    include pad . 'xref/start.php';

?>