<?php

  function padTypeGet ( $item ) {

    if     ( ! padValidTag    ( $item ) )                                return FALSE;
    elseif ( padChkLevel      ( $item                                ) ) return 'level';
    elseif ( padExists        ( APP . "tags/$item.php"               ) ) return 'app';
    elseif ( padExists        ( APP . "tags/$item.html"              ) ) return 'app';
    elseif ( padExists        ( PAD . "tags/$item.php"               ) ) return 'pad';
    elseif ( padExists        ( PAD . "tags/$item.html"              ) ) return 'pad';
    elseif ( isset            ( $GLOBALS ['padFlagStore']    [$item] ) ) return 'flag';
    elseif ( isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return 'content';
    elseif ( isset            ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( isset            ( $GLOBALS ['padSeqStore']     [$item] ) ) return 'store';      
    elseif ( isset            ( $GLOBALS ['padTables']       [$item] ) ) return 'table';
    elseif ( padExists        ( PAD . "tag/$item.php"                ) ) return 'tag';
    elseif ( padArrayCheck    ( $item                                ) ) return 'array';
    elseif ( padFieldCheck    ( $item                                ) ) return 'field';
    elseif ( padIsContentFile ( $item                                ) ) return 'fragment';
    elseif ( padIsPagesFile   ( $item                                ) ) return 'page';
    elseif ( padExists        ( PAD . "sequence/types/$item"         ) ) return 'sequence';
    elseif ( padExists        ( PAD . "sequence/actions/$item.php"   ) ) return 'action';
    elseif ( padDataFileCheck ( $item                                ) ) return 'local';      
    elseif ( defined          ( $item                                ) ) return 'constant';
    elseif ( padExists        ( APP . "functions/$item.php"          ) ) return 'function';
    elseif ( padExists        ( PAD . "functions/$item.php"          ) ) return 'function';
    elseif ( function_exists  ( $item                                ) ) return 'php';
    elseif ( padParmCheck     ( $item                                ) ) return 'parm';
    elseif ( padIsObject      ( $item                                ) ) return 'object';
    elseif ( padIsResource    ( $item                                ) ) return 'resource';
    else                                                                 return FALSE;

  }

  function padTypeCheck ( $type, $item ) {

    if     ( ! padValidType                           ( $type                                ) ) return FALSE;
    if     (                       ! padExists        ( PAD . "types/$type.php"              ) ) return FALSE;
    elseif ( $type == 'app'      and padExists        ( APP . "tags/$item.php"               ) ) return $type;
    elseif ( $type == 'app'      and padExists        ( APP . "tags/$item.html"              ) ) return $type;
    elseif ( $type == 'pad'      and padExists        ( PAD . "tags/$item.php"               ) ) return $type;
    elseif ( $type == 'pad'      and padExists        ( PAD . "tags/$item.html"              ) ) return $type;
    elseif ( $type == 'fragment' and padIsContentFile ( $item                                ) ) return $type;
    elseif ( $type == 'page    ' and padIsPagesFile   ( $item                                ) ) return $type;
    elseif ( $type == 'tag'      and padExists        ( PAD . "tag/$type.php"                ) ) return $type;
    elseif ( $type == 'level'    and padChkLevel      ( $item                                ) ) return $type;
    elseif ( $type == 'flag'     and isset            ( $GLOBALS ['padFlagStore'] [$item]    ) ) return $type;
    elseif ( $type == 'content'  and isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return $type;
    elseif ( $type == 'data'     and isset            ( $GLOBALS ['padDataStore'] [$item]    ) ) return $type;
    elseif ( $type == 'sequence' and padExists        ( PAD . "sequence/types/$item"         ) ) return $type;
    elseif ( $type == 'action'   and padExists        ( PAD . "sequence/actions/$item.php"   ) ) return $type;
    elseif ( $type == 'store'    and isset            ( $GLOBALS ['padSeqStore'] [$item]     ) ) return $type;
    elseif ( $type == 'local'    and padDataFileCheck ( $item                                ) ) return $type;
    elseif ( $type == 'array'    and padArrayCheck    ( $item                                ) ) return $type;
    elseif ( $type == 'field'    and padFieldCheck    ( $item                                ) ) return $type;
    elseif ( $type == 'parm'     and padParmCheck     ( $item                                ) ) return $type;
    elseif ( $type == 'constant' and defined          ( $item                                ) ) return $type;
    elseif ( $type == 'function' and padExists        ( APP . "functions/$item.php"          ) ) return $type;
    elseif ( $type == 'function' and padExists        ( PAD . "functions/$item.php"          ) ) return $type;
    elseif ( $type == 'php'      and function_exists  ( $item                                ) ) return $type;
    elseif ( $type == 'object'   and padIsObject      ( $item                                ) ) return $type;
    elseif ( $type == 'resource' and padIsResource    ( $item                                ) ) return $type;
    elseif ( $type == 'table'    and isset            ( $GLOBALS ['padTables'] [$item]       ) ) return $type;
    else                                                                                         return FALSE;

  }

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( padExists       ( APP . "functions/$type.php"            ) ) return 'app';
    elseif ( padExists       ( PAD . "functions/$type.php"            ) ) return 'pad';
    elseif ( function_exists ( $type                                  ) ) return 'php';
    elseif ( padFieldCheck   ( $type                                  ) ) return 'field';
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$type]      ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type]   ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]      ) ) return 'data';
    elseif ( isset           ( $GLOBALS ['padSeqStore'] [$type]       ) ) return 'sequence';
    elseif ( padTagCheck     ( $type,                                 ) ) return 'tag';
    elseif ( padArrayCheck   ( $type                                  ) ) return 'array';
    elseif ( padParmCheck    ( $type, 1                               ) ) return 'parm';
    elseif ( padChkLevel     ( $type                                  ) ) return 'level';
    elseif ( defined         ( $type                                  ) ) return 'constant';
    elseif ( padExists       ( PAD . "sequence/actions/$type.php"     ) ) return 'action';
    elseif ( padExists       ( APP . "tags/$type.php"                 ) ) return 'ToDo';
    elseif ( padExists       ( APP . "tags/$type.html"                ) ) return 'ToDo';
    elseif ( padExists       ( PAD . "tags/$type.php"                 ) ) return 'ToDo';
    elseif ( padExists       ( PAD . "tags/$type.html"                ) ) return 'ToDo';
    elseif ( padIsObject     ( $type                                  ) ) return 'object';
    elseif ( padIsResource   ( $type                                  ) ) return 'resource';
    else                                                                  return FALSE;

  } 
  
?>