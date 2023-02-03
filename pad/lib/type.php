<?php

  function padTypeGet ( $type ) {

    if     ( ! padValid      ( $type ) )                                  return FALSE;
    elseif ( file_exists     ( APP . "tags/$type.php"                 ) ) return 'app';
    elseif ( file_exists     ( APP . "tags/$type.html"                ) ) return 'app';
    elseif ( file_exists     ( PAD . "pad/tags/$type.php"             ) ) return 'pad';
    elseif ( file_exists     ( PAD . "pad/tags/$type.html"            ) ) return 'pad';
    elseif ( file_exists     ( APP . "fragments/$type.php"            ) ) return 'fragment';
    elseif ( file_exists     ( APP . "fragments/$type.html"           ) ) return 'fragment';
    elseif ( padTagCheck     ( $type,                                 ) ) return 'tag';
    elseif ( padChkLevel     ( $type                                  ) ) return 'level';
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$type]      ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type]   ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]      ) ) return 'data';
    elseif ( file_exists     ( PAD . "pad/sequence/types/$type"       ) ) return 'sequence';
    elseif ( file_exists     ( PAD . "pad/sequence/actions/$type.php" ) ) return 'action';
    elseif ( isset           ( $GLOBALS ['padSeqStore'] [$type]       ) ) return 'store';      
    elseif ( file_exists     ( APP . "data/$type"                     ) ) return 'local';      
    elseif ( file_exists     ( APP . "data/$type.xml"                 ) ) return 'local';
    elseif ( file_exists     ( APP . "data/$type.json"                ) ) return 'local';
    elseif ( file_exists     ( APP . "data/$type.yaml"                ) ) return 'local';
    elseif ( file_exists     ( APP . "data/$type.csv"                 ) ) return 'local';
    elseif ( file_exists     ( APP . "data/$type.php"                 ) ) return 'local';
    elseif ( padArrayCheck   ( $type                                  ) ) return 'array';
    elseif ( padFieldCheck   ( $type                                  ) ) return 'field';
    elseif ( padParmCheck    ( $type, 1                               ) ) return 'parm';
    elseif ( defined         ( $type                                  ) ) return 'constant';
    elseif ( file_exists     ( APP . "functions/$type.php"            ) ) return 'function';
    elseif ( file_exists     ( PAD . "pad/functions/$type.php"        ) ) return 'function';
    elseif ( function_exists ( $type                                  ) ) return 'php';
    elseif ( padIsObject     ( $type                                  ) ) return 'object';
    elseif ( padIsResource   ( $type                                  ) ) return 'resource';
    else                                                                  return FALSE;

  }

  function padTypeCheck ( $type, $item ) {

    if     (                       ! file_exists     ( PAD . "pad/tag/$item.php"              ) ) return FALSE;
    elseif ( $type == 'app'      and file_exists     ( APP . "tags/$item.php"                 ) ) return TRUE;
    elseif ( $type == 'app'      and file_exists     ( APP . "tags/$item.html"                ) ) return TRUE;
    elseif ( $type == 'pad'      and file_exists     ( PAD . "pad/tags/$item.php"             ) ) return TRUE;
    elseif ( $type == 'pad'      and file_exists     ( PAD . "pad/tags/$item.html"            ) ) return TRUE;
    elseif ( $type == 'include'  and file_exists     ( APP . "includes/$item.php"             ) ) return TRUE;
    elseif ( $type == 'include'  and file_exists     ( APP . "includes/$item.html"            ) ) return TRUE;
    elseif ( $type == 'tag'      and padTagCheck     ( $item,                                 ) ) return TRUE;
    elseif ( $type == 'level'    and padChkLevel     ( $item                                  ) ) return TRUE;
    elseif ( $type == 'flag'     and isset           ( $GLOBALS ['padFlagStore'] [$item]      ) ) return TRUE;
    elseif ( $type == 'content'  and isset           ( $GLOBALS ['padContentStore'] [$item]   ) ) return TRUE;
    elseif ( $type == 'data'     and isset           ( $GLOBALS ['padDataStore'] [$item]      ) ) return TRUE;
    elseif ( $type == 'sequence' and file_exists     ( PAD . "pad/sequence/types/$item"       ) ) return TRUE;
    elseif ( $type == 'action'   and file_exists     ( PAD . "pad/sequence/actions/$item.php" ) ) return TRUE;
    elseif ( $type == 'store'    and isset           ( $GLOBALS ['padSeqStore'] [$item]       ) ) return TRUE;
    elseif ( $type == 'array'    and padArrayCheck   ( $item                                  ) ) return TRUE;
    elseif ( $type == 'field'    and padFieldCheck   ( $item                                  ) ) return TRUE;
    elseif ( $type == 'parm'     and padParmCheck    ( $item, 1                               ) ) return TRUE;
    elseif ( $type == 'constant' and defined         ( $item                                  ) ) return TRUE;
    elseif ( $type == 'function' and file_exists     ( APP . "functions/$item.php"            ) ) return TRUE;
    elseif ( $type == 'function' and file_exists     ( PAD . "pad/functions/$item.php"        ) ) return TRUE;
    elseif ( $type == 'php'      and function_exists ( $item                                  ) ) return TRUE;
    elseif ( $type == 'object'   and padIsObject     ( $item                                  ) ) return TRUE;
    elseif ( $type == 'resource' and padIsResource   ( $item                                  ) ) return TRUE;
    else                                                                                          return FALSE;

  }

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( file_exists     ( APP . "functions/$type.php"            ) ) return 'app';
    elseif ( file_exists     ( PAD . "pad/functions/$type.php"        ) ) return 'pad';
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
    elseif ( file_exists     ( PAD . "pad/sequence/actions/$type.php" ) ) return 'action';
    elseif ( file_exists     ( APP . "tags/$type.php"                 ) ) return 'tag';
    elseif ( file_exists     ( APP . "tags/$type.html"                ) ) return 'tag';
    elseif ( file_exists     ( PAD . "pad/tags/$type.php"             ) ) return 'tag';
    elseif ( file_exists     ( PAD . "pad/tags/$type.html"            ) ) return 'tag';
    elseif ( padIsObject     ( $type                                  ) ) return 'object';
    elseif ( padIsResource   ( $type                                  ) ) return 'resource';
    else                                                                  return FALSE;

  } 
  
?>