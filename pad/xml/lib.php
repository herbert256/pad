<?php


  function padXmlStatus ($pad) {

    global $padNull, $padData, $padBase; 

    if     ( $padNull [$pad]                        ) return 'null';
    elseif ( ! count ( $padData [$pad] )            ) return 'no-data';
    elseif ( ! $padBase [$pad]                      ) return 'no-base';
    elseif ( ! padIsDefaultData ( $padData [$pad] ) ) return 'ok-' . count ( $padData [$pad] );
    else                                              return 'ok';

  }


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


?>