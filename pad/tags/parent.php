<?php

  $pData [$p] = pMake_data ( $pData [$p-1] [$pKey[$p-1]] );
    
  foreach ($pData [$p-1] as $pK => $pV)
    if ( $pK <> [$pKey[$p-1]] )
      unset ( $pData [$p-1] [$pK] );

  if ( count ($pData [$p]) )
    return TRUE;
  else
    return FALSE;
  
?>