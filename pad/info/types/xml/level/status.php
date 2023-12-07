<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padTmp = $padBase [$pad];

  if     ( ! $padTmp                                                   ) return '';
  elseif ( $padDouble [$pad]                                           ) return $padDouble [$pad];
  elseif ( $padTmp == $padPadStart [$pad]                              ) return 'content';
  elseif ( $padTmp == $padXmlTrue                                      ) return 'true';
  elseif ( $padTmp == $padXmlFalse                                     ) return 'false';
  elseif ( $padTmp == $padXmlOb                                        ) return 'ob';
  elseif ( $padTmp == $padXmlTagContent                                ) return 'tagContent';
  elseif ( $padTmp == $padXmlTagResult                                 ) return 'tagResult';

  elseif ( $padXmlOb and $padTmp == $padPadStart [$pad] . $padXmlOb    ) return 'content+ob';
  elseif ( $padXmlOb and $padTmp == $padXmlTrue . $padXmlOb            ) return 'true+ob';
  elseif ( $padXmlOb and $padTmp == $padBase [$pad] . $padXmlOb        ) return 'true+ob-2';
  elseif ( $padXmlOb and $padTmp == $padXmlFalse . $padXmlOb           ) return 'false+ob';
  elseif ( $padXmlOb and $padTmp == $padFalse . $padXmlOb       ) return 'false+ob-2';
  elseif ( $padXmlOb and $padTmp == $padXmlTagContent . $padXmlOb      ) return 'tagContent+ob';
  elseif ( $padXmlOb and $padTmp == $padTagContent . $padXmlOb         ) return 'tagContent+ob-2';
  elseif ( $padXmlOb and $padTmp == $tagContent . $padXmlOb            ) return 'content+ob-2';

  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padXmlTagContent . $padXmlTagResult             ) return 'true+result';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padXmlTagContent . $padTagResult                ) return 'true+result-2';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padTagContent . $padXmlTagResult                ) return 'true+result-3';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padTagContent . $padTagResult                   ) return 'true+result-4';

  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padXmlTagContent . $padXmlOb . $padXmlTagResult ) return 'true+ob+result';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padXmlTagContent . $padXmlOb . $padTagResult    ) return 'true+ob+result-2';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padTagContent . $padXmlOb . $padXmlTagResult    ) return 'true+ob+result-3';
  elseif ( $padXmlTagReturn == 'value' and
           $padTmp == $padTagContent . $padXmlOb . $padTagResult       ) return 'true+ob+result-4';

  elseif ( $padTmp == $padTagContent                                   ) return 'tagContent-2';
  elseif ( $padXmlTagReturn == 'value' and $padTmp == $padTagResult    ) return 'tagResult-2';
  elseif ( $padXmlTagReturn == 'value' and $padTmp == $padXmlTagResult ) return 'tagResult-3';

  elseif ( $padTmp == $padBase [$pad]                                  ) return 'true-2';
  elseif ( $padTmp == $padFalse                                 ) return 'false-2';

  elseif ( str_starts_with ( $padTag [$pad], 'entry')                  ) return 'entry';
  elseif ( padOpenCloseOk ( $padTmp, '@start@' )                       ) return 'before_@start@';
  elseif ( $padTmp == $padContent                                      ) return 'content-2';

  else                                                                   return 'WTF';

?>