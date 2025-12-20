<?php

  function padTypeCommon ( $item ) {

    if     ( isset              ( $GLOBALS ['pqStore']         [$item] ) ) return 'pull';
    elseif ( isset              ( $GLOBALS ['padBoolStore']    [$item] ) ) return 'flag';
    elseif ( isset              ( $GLOBALS ['padContentStore'] [$item] ) ) return 'content';
    elseif ( isset              ( $GLOBALS ['padSelect']       [$item] ) ) return 'select';
    elseif ( isset              ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( padAppIncludeCheck ( $item                                ) ) return 'include';
    elseif ( padFieldCheck      ( $item                                ) ) return 'field';
    elseif ( padTagCheck        ( $item                                ) ) return 'property';
    elseif ( padArrayCheck      ( $item                                ) ) return 'array';
    elseif ( padOptCheck        ( $item, 1                             ) ) return 'parm';
    elseif ( padChkLevel        ( $item                                ) ) return 'level';
    elseif ( defined            ( $item                                ) ) return 'constant';
    elseif ( padDataFileName    ( $item                                ) ) return 'local';
    elseif ( padScriptCheck     ( $item                                ) ) return 'script';
    elseif ( function_exists    ( $item                                ) ) return 'php';
    elseif ( file_exists        ( PT . $item                           ) ) return 'sequence';
    elseif ( file_exists        ( PQ . "actions/types/$item.php"       ) ) return 'action';

    return FALSE;

  }

  function padTypeTag ( $type, $goFunction=1 ) {

    if     ( ! padValid     ( $type                  ) ) return FALSE;
    elseif ( padAppTagCheck ( $type                  ) ) return 'app';
    elseif ( file_exists    ( PAD . "tags/$type.php" ) ) return 'pad';
    elseif ( file_exists    ( PAD . "tags/$type.pad" ) ) return 'pad';

    $common = padTypeCommon ( $type );
    if ( $common )
      return $common;

    if ( $goFunction and padTypeFunction ( $type, 0 ) )
      return 'function';

    return FALSE;

  }

  function padTypeTagCheck ( $type, $item ) {

    if     ( padAppTagCheck     ( $item                                ) and $item == 'app'      ) return $type;
    elseif ( file_exists        ( PAD . "tags/$item.php"               ) and $type == 'pad'      ) return $type;
    elseif ( file_exists        ( PAD . "tags/$item.pad"               ) and $type == 'pad'      ) return $type;
    elseif ( isset              ( $GLOBALS ['pqStore']         [$item] ) and $type == 'pull'     ) return $type;
    elseif ( isset              ( $GLOBALS ['padBoolStore']    [$item] ) and $type == 'flag'     ) return $type;
    elseif ( isset              ( $GLOBALS ['padContentStore'] [$item] ) and $type == 'content'  ) return $type;
    elseif ( isset              ( $GLOBALS ['padSelect']       [$item] ) and $type == 'select'   ) return $type;
    elseif ( isset              ( $GLOBALS ['padDataStore']    [$item] ) and $type == 'data'     ) return $type;
    elseif ( padAppIncludeCheck ( $item                                ) and $type == 'include'  ) return $type;
    elseif ( padFieldCheck      ( $item                                ) and $type == 'field'    ) return $type;
    elseif ( padTagCheck        ( $item                                ) and $type == 'property' ) return $type;
    elseif ( padArrayCheck      ( $item                                ) and $type == 'array'    ) return $type;
    elseif ( padOptCheck        ( $item, 1                             ) and $type == 'parm'     ) return $type;
    elseif ( padChkLevel        ( $item                                ) and $type == 'level'    ) return $type;
    elseif ( defined            ( $item                                ) and $type == 'constant' ) return $type;
    elseif ( padDataFileName    ( $item                                ) and $type == 'local'    ) return $type;
    elseif ( padScriptCheck     ( $item                                ) and $type == 'script'   ) return $type;
    elseif ( function_exists    ( $item                                ) and $type == 'php'      ) return $type;
    elseif ( file_exists        ( PT . $item                           ) and $type == 'sequence' ) return $type;
    elseif ( file_exists        ( PQ . "actions/types/$item.php"       ) and $type == 'action'   ) return $type;
    elseif ( padTypeFunction    ( $item, 0                             ) and $type == 'function' ) return $type;

    return FALSE;

  }

  function padTypeFunction ( $type, $goTag=1 ) {

        if ( ! padValid          ( $type                       ) ) return FALSE;
    elseif ( padAppFunctionCheck ( $type                       ) ) return 'app';
    elseif ( file_exists         ( PAD . "functions/$type.php" ) ) return 'pad';

    $common = padTypeCommon ( $type );
    if ( $common )
      return $common;

    if ( $goTag and padTypeTag ( $type, 0 ) )
      return 'tag';

    return FALSE;

  }

  function padTypeSeq ( $type, $item ) {

    $GLOBALS ['padTypeSeq'] = $type;

    if ( $type == 'action' and file_exists ( PQ . "actions/types/$item.php" )                           ) return 'action';
    if ( $item == 'action' and file_exists ( PQ . "actions/types/$type.php" )                           ) return 'action';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ( PQ . "actions/types/$item.php")       ) return 'action';
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ( PQ . "actions/types/$type.php")       ) return 'action';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ( PT . "$item")                         ) return 'make';
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ( PT . "$type")                         ) return 'make';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ( PQ . "start/types/$item.php")         ) return $item;
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ( PQ . "start/types/$type.php")         ) return $type;

    if ( file_exists ( PT . "$item" )    and file_exists ( PQ . "start/types/$type.php")                ) return $type;
    if ( file_exists ( PT . "$type" )    and file_exists ( PQ . "start/types/$item.php")                ) return $item;

    if ( file_exists ( PQ . "actions/types/$item.php" ) and file_exists ( PQ . "start/types/$type.php") ) return $type;
    if ( file_exists ( PQ . "actions/types/$type.php" ) and file_exists ( PQ . "start/types/$item.php") ) return $item;

    return FALSE;

  }

?>
