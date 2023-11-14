<?php
 
  $padXmlEvent ['event'] = $padXmlEventType;
  $padXmlEvent ['tree']  = $padXmlLevel [$pad];
  $padXmlEvent ['occur'] = $padOccur    [$pad];

  $padXmlEvents [] = $padXmlEvent;

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlP1 = ( $padXmlOcc == 0 or $padXmlOcc == 99999 ) ? '' : $padXmlOcc;
  $padXmlP2 = str_replace ( 'level-', '', $padXmlEventType ); 
  $padXmlP2 = str_replace ( 'occur-', '', $padXmlP2 ); 

  $padXmlP3 = $pad;
  if ( $padXmlOcc <> 0 and $padXmlOcc <> 99999 )
    $padXmlP3 .= "/$padXmlOcc";

  $padXmlLine = sprintf ( '%-7s',  $padXmlP2      )
              . sprintf ( '%-7s',  $padXmlP3      )
              . sprintf ( '%-7s',  $padXmlLvl     )
              . sprintf ( '%-15s', $padTag [$pad] )
              . ' ';

  if ( $pad > 0 )
    for ( $padI = $pad-1; $padI >= 0; $padI-- )
      $padXmlLine .= $padTag [$padI] . ' ';

  padFilePutContents ( "$padXmlFile/events.txt", substr ( $padXmlLine, 0, 110 ), true );

?>