<?php

  function padGetTypeLvl ( $type ) {

    if     ( ! padValid      ( $type ) )                                 return FALSE;
    elseif ( file_exists     ( APP . "tags/$type.php"                ) ) return 'app';
    elseif ( file_exists     ( APP . "tags/$type.html"               ) ) return 'app';
    elseif ( file_exists     ( PAD . "tags/$type.php"                ) ) return 'pad';
    elseif ( file_exists     ( PAD . "tags/$type.html"               ) ) return 'pad';
    elseif ( file_exists     ( PAD . "tag/$type.php"                 ) ) return 'parm';
    elseif ( padChkLevel     ( $type                                 ) ) return 'level';
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$type]     ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type]  ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]     ) ) return 'data';
    elseif ( file_exists     ( PAD . "sequence/types/$type"          ) ) return 'sequence';
    elseif ( file_exists     ( PAD . "sequence/actions/$type.php"    ) ) return 'action';
    elseif ( isset           ( $GLOBALS ['padSequenceStore'] [$type] ) ) return 'store';
    elseif ( padArrayCheck   ( $type                                 ) ) return 'array';
    elseif ( padFieldCheck   ( $type                                 ) ) return 'field';
    elseif ( defined         ( $type                                 ) ) return 'constant';
    elseif ( file_exists     ( APP . "functions/$type.php"           ) ) return 'function';
    elseif ( file_exists     ( PAD . "functions/$type.php"           ) ) return 'function';
    elseif ( function_exists ( $type                                 ) ) return 'php';
    elseif ( padIsObject     ( $type                                 ) ) return 'object';
    elseif ( padIsResource   ( $type                                 ) ) return 'resource';
    else                                                                 return FALSE;

  }

  function padCheckType ( $type, $name ) {

        if ( ! padValid      ( $type                                 )                         ) return FALSE;
    elseif ( ! padValid      ( $name                                 )                         ) return FALSE;
    elseif ( padChkLevel     ( $name                                 ) and $type == 'level'    ) return TRUE;
    elseif ( file_exists     ( APP . "tags/$name.php"                ) and $type == 'app'      ) return TRUE;
    elseif ( file_exists     ( APP . "tags/$name.html"               ) and $type == 'app'      ) return TRUE;
    elseif ( file_exists     ( PAD . "tags/$name.php"                ) and $type == 'tag'      ) return TRUE;
    elseif ( file_exists     ( PAD . "tags/$name.html"               ) and $type == 'tag'      ) return TRUE;
    elseif ( file_exists     ( PAD . "tag/$name.php"                 ) and $type == 'parm'     ) return TRUE;
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$name]     ) and $type == 'flag'     ) return TRUE;
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$name]  ) and $type == 'content'  ) return TRUE;
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$name]     ) and $type == 'data'     ) return TRUE;
    elseif ( file_exists     ( PAD . "sequence/types/$type"          ) and $type == 'sequence' ) return TRUE;
    elseif ( file_exists     ( PAD . "sequence/actions/$type.php"    ) and $type == 'action'   ) return TRUE;
    elseif ( isset           ( $GLOBALS ['padSequenceStore'] [$name] ) and $type == 'store'    ) return TRUE;
    elseif ( padArrayCheck   ( $name                                 ) and $type == 'array'    ) return TRUE;
    elseif ( padFieldCheck   ( $name                                 ) and $type == 'field'    ) return TRUE;
    elseif ( defined         ( $name                                 ) and $type == 'constant' ) return TRUE;
    elseif ( file_exists     ( APP . "functions/$name.php"           ) and $type == 'function' ) return TRUE;
    elseif ( file_exists     ( PAD . "functions/$name.php"           ) and $type == 'function' ) return TRUE;
    elseif ( function_exists ( $name                                 ) and $type == 'php'      ) return TRUE;
    elseif ( padIsObject     ( $name                                 ) and $type == 'object'   ) return TRUE;
    elseif ( padIsResource   ( $type                                 ) and $type == 'resource' ) return TRUE;
    else                                                                                         return FALSE;

  }

  function padGetTypeEval ( $type ) {

        if ( ! padValid        ( $type                              ) ) return FALSE;
    elseif ( file_exists     ( APP . "functions/$type.php"        ) ) return 'app';
    elseif ( file_exists     ( PAD . "functions/$type.php"        ) ) return 'pad';
    elseif ( function_exists ( $type                              ) ) return 'php';
    elseif ( padFieldCheck    ( $type                              ) ) return 'field';
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$type]     ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type]  ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]     ) ) return 'data';
    elseif ( isset           ( $GLOBALS ['padSequenceStore'] [$type] ) ) return 'sequence';
    elseif ( file_exists     ( PAD . "tag/$type.php"              ) ) return 'parm';
    elseif ( padArrayCheck    ( $type                              ) ) return 'array';
    elseif ( padChkLevel  ( $type                              ) ) return 'level';
    elseif ( defined         ( $type                              ) ) return 'constant';
    elseif ( file_exists     ( PAD . "sequence/actions/$type.php" ) ) return 'action';
    elseif ( file_exists     ( APP . "tags/$type.php"             ) ) return 'tag';
    elseif ( file_exists     ( APP . "tags/$type.html"            ) ) return 'tag';
    elseif ( file_exists     ( PAD . "tags/$type.php"             ) ) return 'tag';
    elseif ( file_exists     ( PAD . "tags/$type.html"            ) ) return 'tag';
    elseif ( padIsObject      ( $type                              ) ) return 'object';
    elseif ( padIsResource    ( $type                              ) ) return 'resource';
    else                                                              return FALSE;

  } 
  
?>