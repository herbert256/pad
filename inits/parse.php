<?php

  if ( $padParse and ! isset($_REQUEST['padParse']) ) {
    
    $padParseInclude = ( isset($_REQUEST['padInclude']) ) ? '1' : '0';

    $padParseGet = padComplete ($padApp, $padPage, '&padParse=1', $padParseInclude);    

    if ( $padParseGet ['result'] <> 200 ) {
      echo "<pre>";
      $padParseData = $padParseGet ['data'];
      unset ( $padParseGet ['data'] ); 
      padDumpArray ( 'curl',  $padParseGet );
      echo "</pre><hr>";
      echo $padParseData;
      padExit();
    }
    
    $padParse = FALSE;
  
  }

  if ( $padParse ) {
    $padLog   = FALSE;
    $padTrace = FALSE;
  }

 ?>