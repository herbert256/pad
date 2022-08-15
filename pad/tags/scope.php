 <?php

  if ( ! $padPrm [$pad] )
    return pError ('First parameter is required ($var)');

  if ( substr($padPrm [$pad], 0, 1) <> '$' )
    return pError ('First parameter must start with $');
  
  if ( ! pArray_check ($padPrm [$pad]) )
    return pError ('First parameter must be an array');

  $padData [$pad] [1] = pArray_value ($padPrm [$pad]);
    
  return TRUE;
  
?>