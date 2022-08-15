 <?php

  if ( ! $padPrm [$pad] )
    return padError ('First parameter is required ($var)');

  if ( substr($padPrm [$pad], 0, 1) <> '$' )
    return padError ('First parameter must start with $');
  
  if ( ! padArrayCheck ($padPrm [$pad]) )
    return padError ('First parameter must be an array');

  $padData [$pad] [1] = padArrayValue ($padPrm [$pad]);
    
  return TRUE;
  
?>