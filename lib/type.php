<?php

  function padTypeGet ( $item ) {

    if     ( ! padValidTag    ( $item ) )                                return FALSE;
    elseif ( padChkLevel      ( $item                                ) ) return 'level';
    elseif ( padExists        ( padApp . "_tags/$item.php"           ) ) return 'app';
    elseif ( padExists        ( padApp . "_tags/$item.html"          ) ) return 'app';
    elseif ( padExists        ( pad . "_tags/$item.php"              ) ) return 'pad';
    elseif ( padExists        ( pad . "_tags/$item.html"             ) ) return 'pad';
    elseif ( isset            ( $GLOBALS ['padFlagStore']    [$item] ) ) return 'flag';
    elseif ( isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return 'content';
    elseif ( isset            ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( isset            ( $GLOBALS ['padSeqStore']     [$item] ) ) return 'store';      
    elseif ( isset            ( $GLOBALS ['padTables']       [$item] ) ) return 'table';
    elseif ( padExists        ( pad . "tag/$item.php"                ) ) return 'tag';
    elseif ( padArrayCheck    ( $item                                ) ) return 'array';
    elseif ( padFieldCheck    ( $item                                ) ) return 'field';
    elseif ( padInclFileName  ( $item                                ) ) return 'include';
    elseif ( padIsPagesFile   ( $item                                ) ) return 'page';
    elseif ( padExists        ( pad . "sequence/types/$item"         ) ) return 'sequence';
    elseif ( padExists        ( pad . "sequence/actions/$item.php"   ) ) return 'action';
    elseif ( padDataFileName  ( $item                                ) ) return 'local';      
    elseif ( defined          ( $item                                ) ) return 'constant';
    elseif ( padExists        ( padApp . "_functions/$item.php"      ) ) return 'function';
    elseif ( padExists        ( pad . "_functions/$item.php"         ) ) return 'function';
    elseif ( function_exists  ( $item                                ) ) return 'php';
    elseif ( padParmCheck     ( $item                                ) ) return 'parm';
    elseif ( padIsObject      ( $item                                ) ) return 'object';
    elseif ( padIsResource    ( $item                                ) ) return 'resource';
    else                                                                 return FALSE;

  }

  function padTypeCheck ( $type, $item ) {

    if     ( ! padValidType                           ( $type                                ) ) return FALSE;
    if     (                       ! padExists        ( pad . "types/$type.php"              ) ) return FALSE;
    elseif ( $type == 'app'      and padExists        ( padApp . "_tags/$item.php"           ) ) return $type;
    elseif ( $type == 'app'      and padExists        ( padApp . "_tags/$item.html"          ) ) return $type;
    elseif ( $type == 'pad'      and padExists        ( pad . "_tags/$item.php"              ) ) return $type;
    elseif ( $type == 'pad'      and padExists        ( pad . "_tags/$item.html"             ) ) return $type;
    elseif ( $type == 'include'  and ppadInclFileName ( $item                                ) ) return $type;
    elseif ( $type == 'page'     and padIsPagesFile   ( $item                                ) ) return $type;
    elseif ( $type == 'tag'      and padExists        ( pad . "tag/$type.php"                ) ) return $type;
    elseif ( $type == 'level'    and padChkLevel      ( $item                                ) ) return $type;
    elseif ( $type == 'flag'     and isset            ( $GLOBALS ['padFlagStore'] [$item]    ) ) return $type;
    elseif ( $type == 'content'  and isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return $type;
    elseif ( $type == 'data'     and isset            ( $GLOBALS ['padDataStore'] [$item]    ) ) return $type;
    elseif ( $type == 'sequence' and padExists        ( pad . "sequence/types/$item"         ) ) return $type;
    elseif ( $type == 'action'   and padExists        ( pad . "sequence/actions/$item.php"   ) ) return $type;
    elseif ( $type == 'store'    and isset            ( $GLOBALS ['padSeqStore'] [$item]     ) ) return $type;
    elseif ( $type == 'local'    and padDataFileName  ( $item                                ) ) return $type;
    elseif ( $type == 'array'    and padArrayCheck    ( $item                                ) ) return $type;
    elseif ( $type == 'field'    and padFieldCheck    ( $item                                ) ) return $type;
    elseif ( $type == 'parm'     and padParmCheck     ( $item                                ) ) return $type;
    elseif ( $type == 'constant' and defined          ( $item                                ) ) return $type;
    elseif ( $type == 'function' and padExists        ( padApp . "_functions/$item.php"      ) ) return $type;
    elseif ( $type == 'function' and padExists        ( pad . "_functions/$item.php"         ) ) return $type;
    elseif ( $type == 'php'      and function_exists  ( $item                                ) ) return $type;
    elseif ( $type == 'object'   and padIsObject      ( $item                                ) ) return $type;
    elseif ( $type == 'resource' and padIsResource    ( $item                                ) ) return $type;
    elseif ( $type == 'table'    and isset            ( $GLOBALS ['padTables'] [$item]       ) ) return $type;
    else                                                                                         return FALSE;

  }

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( padExists       ( padApp . "_functions/$type.php"        ) ) return 'app';
    elseif ( padExists       ( pad . "_functions/$type.php"           ) ) return 'pad';
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
    elseif ( padExists       ( pad . "sequence/actions/$type.php"     ) ) return 'action';
    elseif ( padExists       ( padApp . "_tags/$type.php"             ) ) return 'ToDo';
    elseif ( padExists       ( padApp . "_tags/$type.html"            ) ) return 'ToDo';
    elseif ( padExists       ( pad . "_tags/$type.php"                ) ) return 'ToDo';
    elseif ( padExists       ( pad . "_tags/$type.html"               ) ) return 'ToDo';
    elseif ( padInclFileName ( $type                                  ) ) return 'ToDo';
    elseif ( padDataFileName ( $type                                  ) ) return 'ToDo';      
    elseif ( padIsObject     ( $type                                  ) ) return 'object';
    elseif ( padIsResource   ( $type                                  ) ) return 'resource';
 
    else                                                                  return FALSE;

  } 
  
?>