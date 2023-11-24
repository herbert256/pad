<?php
  
  $padXrefTagContent = $padContent;
  $padXrefOb         = $padTagOb;
  $padXrefTagResult  = $padTagResult;
  $padXrefTrue       = $padTrue [$pad];
  $padXrefFalse      = $padFalse [$pad];
  
  if     ( is_object   ( $padTagResult ) ) $padXrefReturn = 'object';
  elseif ( is_resource ( $padTagResult ) ) $padXrefReturn = 'resource';
  elseif ( is_array    ( $padTagResult ) ) $padXrefReturn = 'array';
  elseif ( $padTagResult === TRUE        ) $padXrefReturn = 'true';
  elseif ( $padTagResult === FALSE       ) $padXrefReturn = 'false';
  elseif ( $padTagResult === NULL        ) $padXrefReturn = 'null';
  elseif ( $padTagResult === INF         ) $padXrefReturn = 'inf';
  elseif ( $padTagResult === NAN         ) $padXrefReturn = 'nan';
  elseif ( strlen ($padTagResult ) == 0  ) $padXrefReturn = 'empty';
  else                                     $padXrefReturn = 'value';
  
  include pad . 'tail/types/xref/items/return.php';
  
?>