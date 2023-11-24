<?php

  $padTmp = $padBase [$pad];

  if     ( $padNull [$pad]                                                ) return 'null';
  elseif ( ! $padTmp                                                      ) return 'NONE';
  elseif ( $padTmp == $padPadStart [$pad]                                 ) return 'content';
  elseif ( $padTmp == $padXrefTrue                                        ) return 'true';
  elseif ( $padTmp == $padXrefFalse                                       ) return 'false';
  elseif ( $padTmp == $padXrefOb                                          ) return 'ob';
  elseif ( $padTmp == $padXrefTagContent                                  ) return 'tagContent';
  elseif ( $padTmp == $padXrefTagResult                                   ) return 'tagResult';

  elseif ( $padXrefOb and $padTmp == $padPadStart [$pad] . $padXrefOb     ) return 'content+ob';
  elseif ( $padXrefOb and $padTmp == $padXrefTrue . $padXrefOb            ) return 'true+ob';
  elseif ( $padXrefOb and $padTmp == $padTrue [$pad] . $padXrefOb         ) return 'true+ob-2';
  elseif ( $padXrefOb and $padTmp == $padXrefFalse . $padXrefOb           ) return 'false+ob';
  elseif ( $padXrefOb and $padTmp == $padFalse [$pad] . $padXrefOb        ) return 'false+ob-2';
  elseif ( $padXrefOb and $padTmp == $padXrefTagContent . $padXrefOb      ) return 'tagContent+ob';
  elseif ( $padXrefOb and $padTmp == $padTagContent . $padXrefOb          ) return 'tagContent+ob-2';
  elseif ( $padXrefOb and $padTmp == $tagContent . $padXrefOb             ) return 'content+ob-2';

  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padXrefTagContent . $padXrefTagResult              ) return 'true+result';
  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padXrefTagContent . $padTagResult                  ) return 'true+result-2';
  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padTagContent . $padXrefTagResult                  ) return 'true+result-3';
  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padTagContent . $padTagResult                      ) return 'true+result-4';

  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padXrefTagContent . $padXrefOb . $padXrefTagResult ) return 'true+ob+result';
  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padXrefTagContent . $padXrefOb . $padTagResult     ) return 'true+ob+result-2';
  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padTagContent . $padXrefOb . $padXrefTagResult     ) return 'true+ob+result-3';
  elseif ( $padXrefReturn == 'value' and
           $padTmp == $padTagContent . $padXrefOb . $padTagResult         ) return 'true+ob+result-4';

  elseif ( $padTmp == $padTagContent                                      ) return 'tagContent-2';
  elseif ( $padXrefReturn == 'value' and $padTmp == $padTagResult         ) return 'tagResult-2';
  elseif ( $padXrefReturn == 'value' and $padTmp == $padXrefTagResult     ) return 'tagResult-3';

  elseif ( $padTmp == $padTrue [$pad]                                     ) return 'true-2';
  elseif ( $padTmp == $padFalse [$pad]                                    ) return 'false-2';

  elseif ( str_starts_with ( $padTag [$pad], 'entry')                     ) return 'entry';
  elseif ( padOpenCloseOk ( $padTmp, '@start@' )                          ) return 'before_@start@';
  elseif ( $padTmp == $padContent                                         ) return 'content-2';

  else                                                                      return 'WTF';

?>