<?php

  if ( $padTag [$pad] == 'padBuildData' )
    $padXmlTag [$pad] = str_replace ( '/', '-', $padPage );
  else
    $padXmlTag [$pad] = $padTag [$pad];

  $padXmlOcc [$pad] = '';

  if ( $pad == 0 )
    return;

  $padXmlParms = [
    'type'    => $padType [$pad],
    'parm'    => $padOpt  [$pad] [0],
    'status'  => padStatus ($pad),
    'base'    => padBase ($pad),
    'content' => strlen ( $padPadStart [$pad] ),
    'default' => $padDefault [$pad]
  ];

  if ( ! $padNull [$pad] )
    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padXmlParms ['count'] = count ( $padData [$pad] );

  padXmlWriteOpen ( $padXmlTag [$pad], $padXmlParms );

?>