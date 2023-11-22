<?php
  
  $padXmlTagContent = $padContent;
  $padXmlOb         = $padTagOb;
  $padXmlTagResult  = $padTagResult;
  $padXmlTrue       = $padTrue [$pad];
  $padXmlFalse      = $padFalse [$pad];
  
  if     ( is_object   ( $padTagResult ) ) $padXmlTagReturn = 'object';
  elseif ( is_resource ( $padTagResult ) ) $padXmlTagReturn = 'resource';
  elseif ( is_array    ( $padTagResult ) ) $padXmlTagReturn = 'array';
  elseif ( $padTagResult === TRUE        ) $padXmlTagReturn = 'true';
  elseif ( $padTagResult === FALSE       ) $padXmlTagReturn = 'false';
  elseif ( $padTagResult === NULL        ) $padXmlTagReturn = 'null';
  elseif ( $padTagResult === INF         ) $padXmlTagReturn = 'inf';
  elseif ( $padTagResult === NAN         ) $padXmlTagReturn = 'nan';
  elseif ( strlen ($padTagResult ) == 0  ) $padXmlTagReturn = 'empty';
  else                                     $padXmlTagReturn = 'value';
  
?>