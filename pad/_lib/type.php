<?php


  function padTypeGet ( $item ) {

    if     ( padChkLevel      ( $item                                ) ) return 'level';
    elseif ( file_exists      ( APP . "_tags/$item.php"              ) ) return 'app';
    elseif ( file_exists      ( APP . "_tags/$item.pad"              ) ) return 'app';
    elseif ( file_exists      ( "tags/$item.php"                     ) ) return 'pad';
    elseif ( file_exists      ( "tags/$item.pad"                     ) ) return 'pad';
    elseif ( isset            ( $GLOBALS ['padFlagStore']    [$item] ) ) return 'flag';
    elseif ( isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return 'content';
    elseif ( padContentCheck  ( $item                                ) ) return 'content';
    elseif ( isset            ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( isset            ( $GLOBALS ['padTables']       [$item] ) ) return 'table';
    elseif ( file_exists      ( "tag/$item.php"                      ) ) return 'tag';
    elseif ( padDataFileName  ( $item                                ) ) return 'local';    
    elseif ( padArrayCheck    ( $item, 1                             ) ) return 'array';
    elseif ( padFieldCheck    ( $item, 1                             ) ) return 'field';
    elseif ( padInclFileName  ( $item                                ) ) return 'include';
    elseif ( defined          ( $item                                ) ) return 'constant';
    elseif ( file_exists      ( APP . "_functions/$item.php"         ) ) return 'function';
    elseif ( file_exists      ( "functions/$item.php"                ) ) return 'function';
    elseif ( function_exists  ( $item                                ) ) return 'php';
    elseif ( isset            ( $GLOBALS ['padSeqStore'] [$item]     ) ) return 'store';
    elseif ( file_exists      ( "seq/types/$item"                    ) ) return 'seq';
    else                                                                 return FALSE;

  }

  function padTypeCheck ( $type, $item ) {

    if     ( ! padValidType                          ( $type                                ) ) return FALSE;
    if     (                        ! file_exists    ( "types/$type.php"                    ) ) return FALSE;
    elseif ( $type == 'app'      and file_exists     ( APP . "_tags/$item.php"              ) ) return $type;
    elseif ( $type == 'app'      and file_exists     ( APP . "_tags/$item.pad"              ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( "tags/$item.php"                     ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( "tags/$item.pad"                     ) ) return $type;
    elseif ( $type == 'tag'      and file_exists     ( "tag/$type.php"                      ) ) return $type;
    elseif ( $type == 'level'    and padChkLevel     ( $item                                ) ) return $type;
    elseif ( $type == 'flag'     and isset           ( $GLOBALS ['padFlagStore'] [$item]    ) ) return $type;
    elseif ( $type == 'content'  and isset           ( $GLOBALS ['padContentStore'] [$item] ) ) return $type;
    elseif ( $type == 'content'  and padContentCheck ( $item                                ) ) return $type;
    elseif ( $type == 'data'     and isset           ( $GLOBALS ['padDataStore'] [$item]    ) ) return $type;
    elseif ( $type == 'local'    and padDataFileName ( $item                                ) ) return $type;
    elseif ( $type == 'include'  and padInclFileName ( $item                                ) ) return $type;
    elseif ( $type == 'array'    and padArrayCheck   ( $item, 1                             ) ) return $type;
    elseif ( $type == 'field'    and padFieldCheck   ( $item, 1                             ) ) return $type;
    elseif ( $type == 'constant' and defined         ( $item                                ) ) return $type;
    elseif ( $type == 'function' and file_exists     ( APP . "_functions/$item.php"         ) ) return $type;
    elseif ( $type == 'function' and file_exists     ( "functions/$item.php"                ) ) return $type;
    elseif ( $type == 'php'      and function_exists ( $item                                ) ) return $type;
    elseif ( $type == 'table'    and isset           ( $GLOBALS ['padTables'] [$item]       ) ) return $type;
    elseif ( $type == 'store'    and isset           ( $GLOBALS ['padSeqStore'] [$item]     ) ) return $type;
    elseif ( $type == 'seq'      and file_exists     ( "seq/types/$item"                    ) ) return $type;
    else                                                                                        return FALSE;

  }

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( file_exists     ( APP . "_functions/$type.php"           ) ) return 'app';
    elseif ( file_exists     ( "functions/$type.php"                  ) ) return 'pad';
    elseif ( function_exists ( $type                                  ) ) return 'php';
    elseif ( padFieldCheck   ( $type                                  ) ) return 'field';
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$type]      ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type]   ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]      ) ) return 'data';
    elseif ( padTagCheck     ( $type,                                 ) ) return 'tag';
    elseif ( padArrayCheck   ( $type                                  ) ) return 'array';
    elseif ( padOptCheck     ( $type, 1                               ) ) return 'parm';
    elseif ( padChkLevel     ( $type                                  ) ) return 'level';
    elseif ( defined         ( $type                                  ) ) return 'constant';
    elseif ( padInclFileName ( $type                                  ) ) return 'include';
    elseif ( padDataFileName ( $type                                  ) ) return 'local';      
    else                                                                  return FALSE;

  } 
  
?>