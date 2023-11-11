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

    if     ( $padNull [$pad]                               ) return 'null';
    elseif ( count ( $padData [$pad] ) and $padBase [$pad] ) return 'yes';
    elseif ( ! count ( $padData [$pad] )                   ) return 'no';
    elseif ( ! $padBase [$pad]                             ) return 'no';
    else                                                     return 'other';

  }


  function padXmlBase ($pad) {

    global $padBase, $padPadStart, $padTrue, $padFalse, $padXmlOb, $padXmlTagReturn, $padXmlTagResult, $padXmlTrue, $padXmlFalse, $padText; 

    if     ( ! $padBase [$pad]                      ) return 'empty';
    elseif ( $padBase [$pad] == $padPadStart [$pad] ) return 'content';
    elseif ( $padBase [$pad] == $padXmlTrue         ) return 'true';
    elseif ( $padBase [$pad] == $padXmlFalse        ) return 'false';
    elseif ( $padBase [$pad] == $padXmlOb           ) return 'ob';
    elseif ( $padXmlTagReturn == 'value' and
             $padBase [$pad] == $padXmlTagResult    ) return 'return';
    elseif ( $padText [$pad] and
             $padBase [$pad] == $padXmlTagResult    ) return 'text';
    else                                              return 'other';

  }


?>