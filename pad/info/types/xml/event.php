<?php

  $padXmlId++;

  $padXmlEvent ['id']    = $padXmlId;
  $padXmlEvent ['event'] = $padXmlEventType;
  $padXmlEvent ['tree']  = $padXmlLevel [$pad];
  $padXmlEvent ['occur'] = $padOccur    [$pad];

  $padXmlEvents [] = $padXmlEvent;

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlP1 = str_replace ( 'level-', '', $padXmlEventType ); 
  $padXmlP1 = str_replace ( 'occur-', '', $padXmlP1 ); 

  padInfo ( 'xml', $padXmlP1, substr ( $padXmlEventType, 0, 5 ), $padTag [$pad] );

  $padXmlP2 = $pad;
  if ( $padXmlOcc <> 0 and $padXmlOcc <> 99999 )
    $padXmlP2 .= "/$padXmlOcc";

  $padXmlLine = sprintf ( '%-6s',  $padXmlId      )
              . sprintf ( '%-7s',  $padXmlP1      )
              . sprintf ( '%-7s',  $padXmlP2      )
              . sprintf ( '%-7s',  $padXmlLvl     )
              . sprintf ( '%-15s', $padTag [$pad] )
              . ' ';

  if ( $pad > 0 )
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlLine .= $padTag [$padI] . ' ';

  if ( $padXmlDetails)
    padInfoPut ( "$padInfoDir/xml/events.txt", substr ( $padXmlLine, 0, 110 ), true );

?>