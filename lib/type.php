<?php


  function padTypeGet ( $item ) {

    if     ( padChkLevel      ( $item                                ) ) return 'level';
    elseif ( padAppTagCheck   ( $item                                ) ) return 'appTag';
    elseif ( file_exists      ( PAD . "tags/$item.php"               ) ) return 'padTag';
    elseif ( file_exists      ( PAD . "tags/$item.pad"               ) ) return 'padTag';
    elseif ( padContent       ( $item                                ) ) return 'content';
    elseif ( padScriptCheck   ( $item                                ) ) return 'script';
    elseif ( isset            ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( isset            ( $GLOBALS ['padBoolStore']    [$item] ) ) return 'bool';
    elseif ( isset            ( $GLOBALS ['padTables']       [$item] ) ) return 'table';
    elseif ( isset            ( $GLOBALS ['pqStore'] [$item]         ) ) return 'pull';
    elseif ( file_exists      ( PAD . "tag/$item.php"                ) ) return 'tag';
    elseif ( padDataFileName  ( $item                                ) ) return 'local';    
    elseif ( padArrayCheck    ( $item, 1                             ) ) return 'array';
    elseif ( padFieldCheck    ( $item, 1                             ) ) return 'field';
    elseif ( defined          ( $item                                ) ) return 'constant';
    elseif ( padFunctionCheck ( $item                                ) ) return 'appFunction';
    elseif ( padActionCheck   ( $item                                ) ) return 'appAction';
    elseif ( file_exists      ( PAD . "functions/$item.php"          ) ) return 'padFunction';
    elseif ( file_exists      ( PAD . "actions/$item.php"            ) ) return 'padAction';
    elseif ( function_exists  ( $item                                ) ) return 'php';
    elseif ( file_exists      ( PT . "$item"                         ) ) return 'sequence';
    elseif ( file_exists      ( PQ . "actions/types/$item.php"       ) ) return 'action';
    else                                                                 return FALSE;

  }


  function padTypeCheck ( $type, $item ) {

    if     ( ! padValidType                          ( $type                                ) ) return FALSE;
    elseif (                       ! file_exists     ( PAD . "types/$type.php"              ) ) return FALSE;
    elseif ( $type == 'appTag'      and padAppTagCheck  ( $item                                ) ) return $type;
    elseif ( $type == 'padTag'      and file_exists     ( PAD . "tags/$item.php"               ) ) return $type;
    elseif ( $type == 'padTag'      and file_exists     ( PAD . "tags/$item.pad"               ) ) return $type;
    elseif ( $type == 'tag'      and file_exists     ( PAD . "tag/$type.php"                ) ) return $type;
    elseif ( $type == 'script'   and padScriptCheck  ( $item                                ) ) return $type;
    elseif ( $type == 'level'    and padChkLevel     ( $item                                ) ) return $type;
    elseif ( $type == 'bool'     and isset           ( $GLOBALS ['padBoolStore'] [$item]    ) ) return $type;
    elseif ( $type == 'content'  and padContent      ( $item                                ) ) return $type;
    elseif ( $type == 'data'     and isset           ( $GLOBALS ['padDataStore'] [$item]    ) ) return $type;
    elseif ( $type == 'local'    and padDataFileName ( $item                                ) ) return $type;
    elseif ( $type == 'array'    and padArrayCheck   ( $item, 1                             ) ) return $type;
    elseif ( $type == 'field'    and padFieldCheck   ( $item, 1                             ) ) return $type;
    elseif ( $type == 'constant' and defined         ( $item                                ) ) return $type;
    elseif ( $type == 'appFunction' and padFunctionCheck ( $item                                ) ) return $type;
    elseif ( $type == 'appAction'   and padActionCheck  ( $item        ) ) return $type;
    elseif ( $type == 'padFunction' and file_exists     ( PAD . "functions/$type.php"                ) ) return $type;
    elseif ( $type == 'padAction'   and file_exists     ( PAD . "actions/$type.php"                ) ) return $type;
    elseif ( $type == 'php'         and function_exists ( $item                                ) ) return $type;
    elseif ( $type == 'table'    and isset           ( $GLOBALS ['padTables'] [$item]       ) ) return $type;
    else                                                                                        return FALSE;

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