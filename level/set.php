<?php

  if ( $padTag [$pad] == 'set'      ) return;
  if ( $padType [$pad] == 'content' ) return;
  if ( $padPair [$pad]              ) return;
  if ( ! count ( $padSet [$pad] )   ) return;
  if ( ! $padBase [$pad]            ) return;

  $padSetParms = '';

  foreach ( $padSet [$pad] as $padK => $padV ) {

    $padV = str_replace (',', '&comma;', $padV );

    if ( $padSetParms )
      $padSetParms .= ',';
      
    $padSetParms .= " \$$padK='$padV'";

  }

  $padBase [$pad] = "{pad $padSetParms}" . $padBase [$pad] . '{/pad}';

  $padSet [$pad]  = [];

?>