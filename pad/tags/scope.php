 <?php

  if ( ! $pParm )
    return pError ('First parameter is required ($var)');

  if ( substr($pParm, 0, 1) <> '$' )
    return pError ('First parameter must start with $');
  
  if ( ! pArray_check ($pParm) )
    return pError ('First parameter must be an array');

  $pData [$pad] [1] = pArray_value ($pParm);
    
  return TRUE;
  
?>