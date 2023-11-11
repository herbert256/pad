<?php

  $padXmlGo = ( count ( $padData [$pad] ) and $padBase [$pad] ) ? 'yes' : 'no';

  $padXmlParms = [
    'go'      => $padXmlGo,
    'return'  => $padXmlTagReturn,
    'result'  => include pad . 'xml/level/status.php',
    'parm'    => $padOpt  [$pad] [0],
    'type'    => $padType [$pad],
    'content' => strlen ( $padPadStart [$pad] ),
    'default' => $padDefault [$pad]
  ];

  if ( $padXmlOb )
    $padXmlParms ['ob'] = strlen($padXmlOb);

  if ( ! $padNull [$pad] )
    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padXmlParms ['count'] = count ( $padData [$pad] );

    return $padXmlParms;
  
?>