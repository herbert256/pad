<?php

  $padTmp = $padBase [$pad];

  if     ( $padNull [$pad]                                             ) return 'null';
  elseif ( ! count ( $padData [$pad] )                                 ) return 'no-data';
  elseif ( ! $padTmp                                                   ) return 'no-base';
  elseif ( $padTmp == $padPadStart [$pad]                              ) return 'content';
  elseif ( $padTmp == $padXmlTrue                                      ) return 'true';
  elseif ( $padTmp == $padXmlFalse                                     ) return 'false';
  elseif ( $padTmp == $padTrue [$pad]                                  ) return 'true-2';
  elseif ( $padTmp == $padFalse [$pad]                                 ) return 'false-2';
  elseif ( $padTmp == $padXmlOb                                        ) return 'ob';
  elseif ( $padXmlTagReturn == 'value' and $padTmp == $padXmlTagResult ) return 'return';
  elseif ( $padTmp == $padXmlTagContent                                ) return 'tagContent';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padXmlTagContent . $padXmlTagResult             ) return 'true+result';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padXmlTagContent . $padXmlOb . $padXmlTagResult ) return 'true+ob+result';
  elseif ( padOpenCloseOk ( $padBase[$pad], '@start@' )                ) return 'before_@start@';
  elseif ( $padTmp == $padTagContent                                   ) return 'tagContent-2';
  elseif ( $padTmp == $padContent                                      ) return 'content-2';
  elseif ( $padXmlTagReturn == 'value' and $padTmp == $padTagResult    ) return 'return-2';
  else                                                                   return 'XML_PROBLEM';

?>