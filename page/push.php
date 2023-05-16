<?php
   
  $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] = [];

  foreach ($GLOBALS as $key => $val )
    if ( substr($key, 0, 3) == 'pad' )
      if ( is_array ( $GLOBALS [$key]  ) and isset ( $GLOBALS [$key] [$GLOBALS['pad']] ) )
         continue;
      elseif ( $key <> 'padFunctionSave' )
         $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] [$key] = $GLOBALS [$key];

?>