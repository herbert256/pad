<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];

  $padTmp = $padBase [$pad];

  if     ( ! $padTmp                                                      ) return '';
 
  elseif ( $padTmp == $padPadStart [$pad]                                 ) return 'content';
  elseif ( $padTmp == $padTreeTrue                                        ) return 'true';
  elseif ( $padTmp == $padTreeFalse                                       ) return 'false';
  elseif ( $padTmp == $padTreeOb                                          ) return 'ob';
  elseif ( $padTmp == $padTreeTagContent                                  ) return 'tagContent';
  elseif ( $padTmp == $padTreeTagResult                                   ) return 'tagResult';

  elseif ( $padTreeOb and $padTmp == $padPadStart [$pad] . $padTreeOb     ) return 'content+ob';
  elseif ( $padTreeOb and $padTmp == $padTreeTrue . $padTreeOb            ) return 'true+ob';
  elseif ( $padTreeOb and $padTmp == $padTrue [$pad] . $padTreeOb         ) return 'true+ob-2';
  elseif ( $padTreeOb and $padTmp == $padTreeFalse . $padTreeOb           ) return 'false+ob';
  elseif ( $padTreeOb and $padTmp == $padFalse [$pad] . $padTreeOb        ) return 'false+ob-2';
  elseif ( $padTreeOb and $padTmp == $padTreeTagContent . $padTreeOb      ) return 'tagContent+ob';
  elseif ( $padTreeOb and $padTmp == $padTagContent . $padTreeOb          ) return 'tagContent+ob-2';
  elseif ( $padTreeOb and $padTmp == $tagContent . $padTreeOb             ) return 'content+ob-2';

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

  elseif ( $padTmp == $padTagContent                                      ) return 'tagContent-2';
  elseif ( $padTmp == $padContent                                         ) return 'content-2';
  elseif ( $padTreeTagReturn == 'value' and $padTmp == $padTagResult      ) return 'tagResult-2';
  elseif ( $padTreeTagReturn == 'value' and $padTmp == $padTreeTagResult  ) return 'tagResult-3';

  elseif ( $padTmp == $padTrue [$pad]                                     ) return 'true-2';
  elseif ( $padTmp == $padFalse [$pad]                                    ) return 'false-2';

  elseif ( str_starts_with ( $padTag [$pad], 'entry')                     ) return 'entry';
  elseif ( padOpenCloseOk ( $padTmp, '@start@' )                          ) return 'before_@start@';

  else                                                                      return 'WTF';

?>