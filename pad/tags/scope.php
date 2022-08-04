 <?php

  if ( ! $pad_parm )
    return pad_error ('First parameter is required ($var)');

  if ( substr($pad_parm, 0, 1) <> '$' )
    return pad_error ('First parameter must start with $');
  
  if ( ! pad_array_check ($pad_parm) )
    return pad_error ('First parameter must be an array');

  $pad_data [$pad] [1] = pad_array_value ($pad_parm);
    
  return TRUE;
  
?>