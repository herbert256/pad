<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];
  
  $padTreeTagContent = $padContent;
  $padTreeOb         = $padTagOb;
  $padTreeTagResult  = $padTagResult;
  $padTreeTrue       = $padTrue [$pad];
  $padTreeFalse      = $padFalse [$pad];
  
  if     ( is_object   ( $padTagResult ) ) $padTreeTagReturn = 'object';
  elseif ( is_resource ( $padTagResult ) ) $padTreeTagReturn = 'resource';
  elseif ( is_array    ( $padTagResult ) ) $padTreeTagReturn = 'array';
  elseif ( $padTagResult === TRUE        ) $padTreeTagReturn = 'true';
  elseif ( $padTagResult === FALSE       ) $padTreeTagReturn = 'false';
  elseif ( $padTagResult === NULL        ) $padTreeTagReturn = 'null';
  elseif ( $padTagResult === INF         ) $padTreeTagReturn = 'inf';
  elseif ( $padTagResult === NAN         ) $padTreeTagReturn = 'nan';
  elseif ( strlen ($padTagResult ) == 0  ) $padTreeTagReturn = 'empty';
  else                                     $padTreeTagReturn = 'value';
  
?>