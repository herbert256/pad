<?php

  function padTypeCommon ( $item ) {

    global $padBoolStore, $padContentStore, $padDataStore, $padSelect, $pqStore;

    if     ( isset              ( $pqStore         [$item]       ) ) return 'pull';
    elseif ( isset              ( $padBoolStore    [$item]       ) ) return 'flag';
    elseif ( isset              ( $padContentStore [$item]       ) ) return 'content';
    elseif ( isset              ( $padSelect       [$item]       ) ) return 'select';
    elseif ( isset              ( $padDataStore    [$item]       ) ) return 'data';
    elseif ( padAppIncludeCheck ( $item                          ) ) return 'include';
    elseif ( padTagCheck        ( $item                          ) ) return 'property';
    elseif ( padFieldCheck      ( $item                          ) ) return 'field';
    elseif ( padArrayCheck      ( $item                          ) ) return 'array';
    elseif ( padOptCheck        ( $item, 1                       ) ) return 'parm';
    elseif ( padChkLevel        ( $item                          ) ) return 'level';
    elseif ( defined            ( $item                          ) ) return 'constant';
    elseif ( padDataFileName    ( $item                          ) ) return 'local';
    elseif ( padScriptCheck     ( $item                          ) ) return 'script';
    elseif ( function_exists    ( $item                          ) ) return 'php';
    elseif ( file_exists        ( PT . $item                     ) ) return 'sequence';
    elseif ( file_exists        ( PA . "$item.php" ) ) return 'action';

    return FALSE;

  }

  function padTypeTag ( $type, $goFunction=1 ) {

    if     ( ! padValid     ( $type              ) ) return FALSE;
    elseif ( padAppTagCheck ( $type              ) ) return 'app';
    elseif ( padCommonCheck ( $type              ) ) return 'common';
    elseif ( padCheck       ( PAD . "tags/$type" ) ) return 'pad';

    $common = padTypeCommon ( $type );
    if ( $common )
      return $common;

    if ( $goFunction and padTypeFunction ( $type, 0 ) )
      return 'function';

    return FALSE;

  }


  function padTypeTagCheck ( $type, $item ) {

    global $padBoolStore, $padContentStore, $padDataStore, $padSelect, $pqStore;

    if     ( padAppTagCheck     ( $item                          ) and $type == 'app'      ) return $type;
    elseif ( padCommonCheck     ( $item                          ) and $type == 'common'   ) return $type;
    elseif ( padCheck           ( PAD . "tags/$item"             ) and $type == 'pad'      ) return $type;
    elseif ( isset              ( $pqStore         [$item]       ) and $type == 'pull'     ) return $type;
    elseif ( isset              ( $padBoolStore    [$item]       ) and $type == 'flag'     ) return $type;
    elseif ( isset              ( $padContentStore [$item]       ) and $type == 'content'  ) return $type;
    elseif ( isset              ( $padSelect       [$item]       ) and $type == 'select'   ) return $type;
    elseif ( isset              ( $padDataStore    [$item]       ) and $type == 'data'     ) return $type;
    elseif ( padAppIncludeCheck ( $item                          ) and $type == 'include'  ) return $type;
    elseif ( padFieldCheck      ( $item                          ) and $type == 'field'    ) return $type;
    elseif ( padTagCheck        ( $item                          ) and $type == 'property' ) return $type;
    elseif ( padArrayCheck      ( $item                          ) and $type == 'array'    ) return $type;
    elseif ( padOptCheck        ( $item, 1                       ) and $type == 'parm'     ) return $type;
    elseif ( padChkLevel        ( $item                          ) and $type == 'level'    ) return $type;
    elseif ( defined            ( $item                          ) and $type == 'constant' ) return $type;
    elseif ( padDataFileName    ( $item                          ) and $type == 'local'    ) return $type;
    elseif ( padScriptCheck     ( $item                          ) and $type == 'script'   ) return $type;
    elseif ( function_exists    ( $item                          ) and $type == 'php'      ) return $type;
    elseif ( file_exists        ( PT . $item                     ) and $type == 'sequence' ) return $type;
    elseif ( file_exists        ( PA . "$item.php" ) and $type == 'action'   ) return $type;
    elseif ( padTypeFunction    ( $item, 0                       ) and $type == 'function' ) return $type;

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

    global $padTypeSeq, $pqStore;

    $padTypeSeq = $type;

    if ( $type == 'action' and file_exists ( PA . "$item.php" )                           ) return 'action';
    if ( $item == 'action' and file_exists ( PA . "$type.php" )                           ) return 'action';

    if ( isset ( $pqStore [$type] ) and file_exists ( PA . "$item.php")                   ) return 'action';
    if ( isset ( $pqStore [$item] ) and file_exists ( PA . "$type.php")                   ) return 'action';

    if ( isset ( $pqStore [$type] ) and file_exists ( PT . "$item")                       ) return 'make';
    if ( isset ( $pqStore [$item] ) and file_exists ( PT . "$type")                       ) return 'make';

    if ( isset ( $pqStore [$type] ) and file_exists ( PQ . "start/types/$item.php")       ) return $item;
    if ( isset ( $pqStore [$item] ) and file_exists ( PQ . "start/types/$type.php")       ) return $type;

    if ( file_exists ( PT . "$item" )    and file_exists ( PQ . "start/types/$type.php")  ) return $type;
    if ( file_exists ( PT . "$type" )    and file_exists ( PQ . "start/types/$item.php")  ) return $item;

    if ( file_exists ( PA . "$item.php" ) and file_exists ( PQ . "start/types/$type.php") ) return $type;
    if ( file_exists ( PA . "$type.php" ) and file_exists ( PQ . "start/types/$item.php") ) return $item;

    return FALSE;

  }

?>