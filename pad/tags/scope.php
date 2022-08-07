 <?php

  if ( ! $pPrm[$p] )
    return pError ('First parameter is required ($var)');

  if ( substr($pPrm[$p], 0, 1) <> '$' )
    return pError ('First parameter must start with $');
  
  if ( ! pArray_check ($pPrm[$p]) )
    return pError ('First parameter must be an array');

  $pData[$p] [1] = pArray_value ($pPrm[$p]);
    
  return TRUE;
  
?>