<?php

  $padTmp = $padBase [$pad];

  if     ( $padNull [$pad]                                                ) return '';
  elseif ( ! count ( $padData [$pad] )                                    ) return '';
  elseif ( ! $padTmp                                                      ) return '';
 
  elseif ( $padTmp == $padPadStart [$pad]                                 ) return 'content';
  elseif ( $padTmp == $padTreeTrue                                        ) return 'true';
  elseif ( $padTmp == $padTreeFalse                                       ) return 'false';
  elseif ( $padTreeTagReturn == 'value' and $padTmp == $padTreeTagResult  ) return 'return';
  elseif ( $padTmp == $padTreeTagContent                                  ) return 'tagContent';

  elseif ( $padTmp == $padContent                                         ) return 'content-2';
  elseif ( $padTmp == $padTrue [$pad]                                     ) return 'true-2';
  elseif ( $padTmp == $padFalse [$pad]                                    ) return 'false-2';
  elseif ( $padTreeTagReturn == 'value' and $padTmp == $padTagResult      ) return 'return-2';
  elseif ( $padTmp == $padTagContent                                      ) return 'tagContent-2';

  elseif ( padOpenCloseOk ( $padTmp, '@start@' )                          ) return 'before_@start@';
  elseif ( $padTmp == $padTreeOb                                          ) return 'ob';

  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTreeTagContent . $padTreeTagResult              ) return 'true+result';
  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTreeTagContent . $padTagResult                  ) return 'true+result-2';
  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTagContent . $padTreeTagResult                  ) return 'true+result-3';
  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTagContent . $padTagResult                      ) return 'true+result-4';

  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTreeTagContent . $padTreeOb . $padTreeTagResult ) return 'true+ob+result';
  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTreeTagContent . $padTreeOb . $padTagResult     ) return 'true+ob+result-2';
  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTagContent . $padTreeOb . $padTreeTagResult     ) return 'true+ob+result-3';
  elseif ( $padTreeTagReturn == 'value' and
           $padTmp == $padTagContent . $padTreeOb . $padTagResult         ) return 'true+ob+result-4';

  elseif ( str_starts_with ( $padTag [$pad], 'entry')                     ) return 'entry';
  
  else                                                                      return '?????';

?>