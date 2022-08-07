 <?php

  if ( ! $pParm[$p] )
    return pError ('First parameter is required ($var)');

  if ( substr($pParm[$p], 0, 1) <> '$' )
    return pError ('First parameter must start with $');
  
  if ( ! pArray_check ($pParm[$p]) )
    return pError ('First parameter must be an array');

  $pData[$p] [1] = pArray_value ($pParm[$p]);
    
  return TRUE;
  
?>