<?php


  function padXmlOccurs ( $pad ) {

    global $padWalk, $padData, $padBeforeBase, $padAfterBase;

    if ( $padWalk [$pad] == 'next'      
      or count ( $padData [$pad] ) > 1   
      or $padBeforeBase [$pad]           
      or $padAfterBase  [$pad] )

      return 'yes';

    else

      return 'no';

  }

  
  function padXmlWriteOpen ( $xml, $parms=[] ) {

    global $padXmlFile;

    if ( $GLOBALS['padTraceActive'] )
      $parms ['trace'] = $GLOBALS['padTraceLine'];
    
    $more = '';
    foreach ( $parms as $key => $value )
      if ( $value )
        $more .= " $key=\"" . htmlspecialchars($value) . '"';

    padFilePutContents ( $padXmlFile, "<$xml$more>", true );
  
  }


  function padXmlWriteClose ( $xml ) {

    global $padXmlFile;
    
    padFilePutContents ( $padXmlFile, "</$xml>", true );
  
  }


  function padXmlStatus ($pad) {

    global $padNull, $padData, $padBase; 


  }


?>