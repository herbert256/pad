<?php


  function padTypeGet ( $item, $goFunctions=1 ) {

    if     ( padChkLevel      ( $item                                ) ) return 'level';
    elseif ( padAppTagCheck   ( $item                                ) ) return 'app';
    elseif ( file_exists      ( PAD . "tags/$item.php"               ) ) return 'pad';
    elseif ( file_exists      ( PAD . "tags/$item.pad"               ) ) return 'pad';
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
    elseif ( padFunctionCheck ( $item                                ) ) return 'function';
    elseif ( file_exists      ( PAD . "functions/$item.php"          ) ) return 'function';
    elseif ( function_exists  ( $item                                ) ) return 'php';
    elseif ( file_exists      ( PT . "$item"                         ) ) return 'sequence';
    elseif ( file_exists      ( PQ . "actions/types/$item.php"       ) ) return 'action';

    if (  $goFunctions and padGetTypeEval ( $item, 0 )  ) 
      return 'padFunction';
		
		return FALSE;
    
  }


  function padTypeCheck ( $type, $item ) {

    if     ( ! padValidType                          ( $type                                ) ) return FALSE;
    elseif (                       ! file_exists     ( PAD . "types/$type.php"              ) ) return FALSE;
    elseif ( $type == 'app'      and padAppTagCheck  ( $item                                ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( PAD . "tags/$item.php"               ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( PAD . "tags/$item.pad"               ) ) return $type;
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
    elseif ( $type == 'function' and padFunctionCheck ( $item                                ) ) return $type;
    elseif ( $type == 'function' and file_exists     ( PAD . "functions/$type.php"                ) ) return $type;
    elseif ( $type == 'php'      and function_exists ( $item                                ) ) return $type;
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
