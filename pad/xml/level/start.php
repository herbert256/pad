<?php

  if ( $padTag [$pad] == 'padBuildData' )
    $padXmlTag [$pad] = str_replace ( '/', '-', $padPage );
  else
    $padXmlTag [$pad] = $padTag [$pad];

  $padXmlOcc [$pad] = '';

  if ( $pad == 0 )
    return;

  $padXmlParms = [
    'parm'   => $padOpt  [$pad] [0],
    'status' => padXmlStatus ($pad),
    'type'   => $padType [$pad]
  ];

  padXmlWriteOpen ( $padXmlTag [$pad], $padXmlParms );

?>