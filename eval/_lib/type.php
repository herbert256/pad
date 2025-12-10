<?php

  function padGetTypeEval ( $type ) {

        if ( ! padValid      ( $type                                  ) ) return FALSE;
    elseif ( padFunctionCheck ( $type                                 ) ) return 'appFunction';
    elseif ( padActionCheck ( $type                                 ) ) return 'appFunction';
    elseif ( file_exists     ( PAD . "functions/$type.php"            ) ) return 'padFunction';
    elseif ( file_exists     ( PAD . "actions/$type.php"              ) ) return 'padAction';
    elseif ( function_exists ( $type                                  ) ) return 'php';
    elseif ( padFieldCheck   ( $type                                  ) ) return 'field';
    elseif ( isset           ( $GLOBALS ['pqStore'] [$type]           ) ) return 'pull';
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