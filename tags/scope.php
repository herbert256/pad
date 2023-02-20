<?php

  if ( ! $padOpt [$pad] [1] )
    return padError ('First parameter is required ($var)');

  if ( substr($padOpt [$pad] [1], 0, 1) <> '$' )
    return padError ('First parameter must start with $');
  
  if ( ! padArrayCheck ($padOpt [$pad] [1]) )
    return padError ('First parameter must be an array');

  $padData [$pad] [1] = padArrayValue ($padOpt [$pad] [1]);
    
  return TRUE;
  
?>