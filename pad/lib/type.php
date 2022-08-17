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
    elseif ( isset           ( $GLOBALS ['padSeqStore'] [$type]      ) ) return 'store';
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

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                ) ) return FALSE;
    elseif ( file_exists     ( APP . "functions/$type.php"          ) ) return 'app';
    elseif ( file_exists     ( PAD . "functions/$type.php"          ) ) return 'pad';
    elseif ( function_exists ( $type                                ) ) return 'php';
    elseif ( padFieldCheck   ( $type                                ) ) return 'field';
    elseif ( isset           ( $GLOBALS ['padFlagStore'] [$type]    ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type] ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]    ) ) return 'data';
    elseif ( isset           ( $GLOBALS ['padSeqStore'] [$type]     ) ) return 'sequence';
    elseif ( file_exists     ( PAD . "tag/$type.php"                ) ) return 'parm';
    elseif ( padArrayCheck   ( $type                                ) ) return 'array';
    elseif ( padChkLevel     ( $type                                ) ) return 'level';
    elseif ( defined         ( $type                                ) ) return 'constant';
    elseif ( file_exists     ( PAD . "sequence/actions/$type.php"   ) ) return 'action';
    elseif ( file_exists     ( APP . "tags/$type.php"               ) ) return 'tag';
    elseif ( file_exists     ( APP . "tags/$type.html"              ) ) return 'tag';
    elseif ( file_exists     ( PAD . "tags/$type.php"               ) ) return 'tag';
    elseif ( file_exists     ( PAD . "tags/$type.html"              ) ) return 'tag';
    elseif ( padIsObject     ( $type                                ) ) return 'object';
    elseif ( padIsResource   ( $type                                ) ) return 'resource';
    else                                                                return FALSE;

  } 
  
?>