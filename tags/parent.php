<?php

  $padData [$pad] = padMakeData ( $padData [$pad-1] [$padKey[$pad-1]] );
    
  foreach ($padData [$pad-1] as $padK => $padV)
    if ( $padK <> [$padKey[$pad-1]] )
      unset ( $padData [$pad-1] [$padK] );

  if ( count ($padData [$pad]) )
    return TRUE;
  else
    return FALSE;
  
?>