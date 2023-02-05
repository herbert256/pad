<?php

  $padOpenClose = [];
  
  if ( $padGiven [$pad] )
    $padPairTag = $padType [$pad] . ':' . $padTag [$pad];
  else
    $padPairTag = $padTag [$pad];

  $padOpenClose [$padPairTag] = TRUE;

  $pad1 = strpos($padTrue [$pad], '{/', 0);

  while ($pad1 !== FALSE) {

    $pad2 = strpos($padTrue [$pad], '}', $pad1);

    if ( $pad2 !== FALSE ) {

      $pad3 = strpos($padTrue [$pad], ' ', $pad1);
      if ($pad3 !== FALSE and $pad3 < $pad2 )
        $pad2 = $pad3;      

      $padCheckTag = substr($padTrue [$pad], $pad1+2, $pad2-$pad1-2);
      if ( padValidTag ($padCheckTag) )
        $padOpenClose [$padCheckTag] = TRUE;

    }

    $pad1 = strpos($padTrue [$pad], '{/', $pad1+1);

  }

  $pad1 = -1;

  while (1) {

    $pad1 = strpos($padTrue [$pad], '{else}', ++$pad1);

    if ( $pad1 === FALSE )
      return;
    
    $padCheck = substr($padTrue [$pad],0,$pad1);

    foreach ( $padOpenClose as $padCheckTag => $padDummy )
      if ( ( substr_count($padCheck, '{'.$padCheckTag.' ' ) + substr_count($padCheck, '{'.$padCheckTag.'}' ) )
             <> 
           ( substr_count($padCheck, '{/'.$padCheckTag.' ') + substr_count($padCheck, '{/'.$padCheckTag.'}') ) )
        continue 2;

    break;

  }

  $padFalse [$pad] = substr ( $padTrue [$pad], $pad1+6  );
  $padTrue  [$pad] = substr ( $padTrue [$pad], 0, $pad1 );

?>