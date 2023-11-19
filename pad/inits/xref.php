<?php

  if ( $padXref ) {

    include_once pad . 'xref/lib.php';
    
    $padXrefPageSouce = padFileGetContents ( padApp . $padPage . '.pad' );

  }

?>