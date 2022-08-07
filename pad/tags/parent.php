<?php

  $pData [$p] = pMake_data ( $pData [$pad-1] [$pKey[$pad-1]] );
    
  foreach ($pData [$pad-1] as $pK => $pad_v)
    if ( $pK <> [$pKey[$pad-1]] )
      unset ( $pData [$pad-1] [$pK] );

  if ( count ($pData [$p]) )
    return TRUE;
  else
    return FALSE;
  
?>