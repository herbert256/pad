<?php

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( file_exists     ( APP . "_functions/$type.php"           ) ) return 'app';
    elseif ( file_exists     ( "functions/$type.php"                  ) ) return 'pad';
    elseif ( function_exists ( $type                                  ) ) return 'php';
    elseif ( padFieldCheck   ( $type                                  ) ) return 'field';
    elseif ( isset           ( $GLOBALS ['padBoolStore'] [$type]      ) ) return 'flag';
    elseif ( isset           ( $GLOBALS ['padContentStore'] [$type]   ) ) return 'content';
    elseif ( isset           ( $GLOBALS ['padDataStore'] [$type]      ) ) return 'data';
    elseif ( padTagCheck     ( $type,                                 ) ) return 'tag';
    elseif ( padArrayCheck   ( $type                                  ) ) return 'array';
    elseif ( padOptCheck     ( $type, 1                               ) ) return 'parm';
    elseif ( padChkLevel     ( $type                                  ) ) return 'level';
    elseif ( defined         ( $type                                  ) ) return 'constant';
    elseif ( padDataFileName ( $type                                  ) ) return 'local';      
    else                                                                  return FALSE;

  } 

?>