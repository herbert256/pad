<?php

  function padTypeGet ( $type ) {

    if     ( ! padValidTag    ( $type ) )                                  return FALSE;
    elseif ( padChkLevel      ( $type                                  ) ) return 'level';
    elseif ( file_exists      ( APP . "tags/$type.php"                 ) ) return 'app';
    elseif ( file_exists      ( APP . "tags/$type.html"                ) ) return 'app';
    elseif ( file_exists      ( PAD . "tags/$type.php"             ) ) return 'pad';
    elseif ( file_exists      ( PAD . "tags/$type.html"            ) ) return 'pad';
    elseif ( isset            ( $GLOBALS ['padFlagStore']    [$type]   ) ) return 'flag';
    elseif ( isset            ( $GLOBALS ['padContentStore'] [$type]   ) ) return 'content';
    elseif ( isset            ( $GLOBALS ['padDataStore']    [$type]   ) ) return 'data';
    elseif ( isset            ( $GLOBALS ['padSeqStore']     [$type]   ) ) return 'store';      
    elseif ( file_exists      ( PAD . "tag/$type.php"              ) ) return 'tag';
    elseif ( padArrayCheck    ( $type                                  ) ) return 'array';
    elseif ( padFieldCheck    ( $type                                  ) ) return 'field';
    elseif ( padIsContentFile ( $type                                  ) ) return 'fragment';
    elseif ( file_exists      ( PAD . "sequence/types/$type"       ) ) return 'sequence';
    elseif ( file_exists      ( PAD . "sequence/actions/$type.php" ) ) return 'action';
    elseif ( padIsDataFile    ( $type                                  ) ) return 'local';      
    elseif ( defined          ( $type                                  ) ) return 'constant';
    elseif ( file_exists      ( APP . "functions/$type.php"            ) ) return 'function';
    elseif ( file_exists      ( PAD . "functions/$type.php"        ) ) return 'function';
    elseif ( function_exists  ( $type                                  ) ) return 'php';
    elseif ( padParmCheck     ( $type, 1                               ) ) return 'parm';
    elseif ( padIsObject      ( $type                                  ) ) return 'object';
    elseif ( padIsResource    ( $type                                  ) ) return 'resource';
    else                                                                  return FALSE;

  }

  function padTypeCheck ( $type, $item ) {

    if     (                       ! file_exists      ( PAD . "tag/$item.php"              ) ) return FALSE;
    elseif ( $type == 'app'      and file_exists      ( APP . "tags/$item.php"                 ) ) return $type;
    elseif ( $type == 'app'      and file_exists      ( APP . "tags/$item.html"                ) ) return $type;
    elseif ( $type == 'pad'      and file_exists      ( PAD . "tags/$item.php"             ) ) return $type;
    elseif ( $type == 'pad'      and file_exists      ( PAD . "tags/$item.html"            ) ) return $type;
    elseif ( $type == 'fragment' and padIsContentFile ( APP . "content/$item"                  ) ) return $type;
    elseif ( $type == 'tag'      and file_exists      ( PAD . "tag/$type.php"              ) ) return $type;
    elseif ( $type == 'level'    and padChkLevel      ( $item                                  ) ) return $type;
    elseif ( $type == 'flag'     and isset            ( $GLOBALS ['padFlagStore'] [$item]      ) ) return $type;
    elseif ( $type == 'content'  and isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return $type;
    elseif ( $type == 'data'     and isset            ( $GLOBALS ['padDataStore'] [$item]      ) ) return $type;
    elseif ( $type == 'sequence' and file_exists      ( PAD . "sequence/types/$item"       ) ) return $type;
    elseif ( $type == 'action'   and file_exists      ( PAD . "sequence/actions/$item.php" ) ) return $type;
    elseif ( $type == 'store'    and isset            ( $GLOBALS ['padSeqStore'] [$item]       ) ) return $type;
    elseif ( $type == 'local'    and padIsDataFile    ( $item                                  ) ) return $type;
    elseif ( $type == 'array'    and padArrayCheck    ( $item                                  ) ) return $type;
    elseif ( $type == 'field'    and padFieldCheck    ( $item                                  ) ) return $type;
    elseif ( $type == 'parm'     and padParmCheck     ( $item, 1                               ) ) return $type;
    elseif ( $type == 'constant' and defined          ( $item                                  ) ) return $type;
    elseif ( $type == 'function' and file_exists      ( APP . "functions/$item.php"            ) ) return $type;
    elseif ( $type == 'function' and file_exists      ( PAD . "functions/$item.php"        ) ) return $type;
    elseif ( $type == 'php'      and function_exists  ( $item                                  ) ) return $type;
    elseif ( $type == 'object'   and padIsObject      ( $item                                  ) ) return $type;
    elseif ( $type == 'resource' and padIsResource    ( $item                                  ) ) return $type;
    else                                                                                           return FALSE;

  }

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( file_exists     ( APP . "functions/$type.php"            ) ) return 'app';
    elseif ( file_exists     ( PAD . "functions/$type.php"        ) ) return 'pad';
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
    elseif ( file_exists     ( PAD . "sequence/actions/$type.php" ) ) return 'action';
    elseif ( file_exists     ( APP . "tags/$type.php"                 ) ) return 'tag';
    elseif ( file_exists     ( APP . "tags/$type.html"                ) ) return 'tag';
    elseif ( file_exists     ( PAD . "tags/$type.php"             ) ) return 'tag';
    elseif ( file_exists     ( PAD . "tags/$type.html"            ) ) return 'tag';
    elseif ( padIsObject     ( $type                                  ) ) return 'object';
    elseif ( padIsResource   ( $type                                  ) ) return 'resource';
    else                                                                  return FALSE;

  } 
  
?>