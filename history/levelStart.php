<?php
  
 $padHstNow  = padHstGetLevel ($pad);

  if ( $padHstShort ) {

    if ( $padHstNow ['base'] and $padHstNow ['base'] == $padHstNow ['true'] ) 
      $padHstNow ['base'] = '*TRUE*';
       
    if ( $padHstNow ['base'] and $padHstNow ['base'] == $padHstNow ['false'] ) 
      $padHstNow ['base'] = '*FALSE*';

    if ( $padHstNow ['name'] == $padHstNow ['tag'] ) 
      unset ( $padHstNow ['name'] );

    if ( $padHstNow ['walk'] == 'start' ) 
      unset ( $padHstNow ['walk'] );

    if ( $padHstNow ['p-type'] == 'none' ) 
      unset ( $padHstNow ['p-type'] );

    foreach ( $padHstNow ['opt'] as $padK => $padV )
      $padHstNow ["Opt: $padK"] = $padV;
    foreach ( $padHstNow ['prm'] as $padK => $padV )
      $padHstNow ["Prm: $padK"] = $padV;
    foreach ( $padHstNow ['set'] as $padK => $padV )
      $padHstNow ["Set: $padK"] = $padV;

    unset ( $padHstNow ['opt'] );
    unset ( $padHstNow ['prm'] );
    unset ( $padHstNow ['set'] );
    unset ( $padHstNow ['pad'] );
    unset ( $padHstPnt [$pad] ['pad'] );

    $padHstNow = padArrayClean ($padHstNow);

  }

  foreach ( $padHstNow as $padK => $padV )
    $padHstPnt [$pad] [$padK] = $padV;

?>