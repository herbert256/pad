<?php


  function padTypeGet ( $item ) {

    if     ( padChkLevel      ( $item                                ) ) return 'level';
    elseif ( file_exists      ( APP . "_tags/$item.php"              ) ) return 'app';
    elseif ( file_exists      ( APP . "_tags/$item.pad"              ) ) return 'app';
    elseif ( file_exists      ( "tags/$item.php"                     ) ) return 'pad';
    elseif ( file_exists      ( "tags/$item.pad"                     ) ) return 'pad';
    elseif ( isset            ( $GLOBALS ['padContentStore'] [$item] ) ) return 'content';
    elseif ( padContentCheck  ( $item                                ) ) return 'defined';
    elseif ( isset            ( $GLOBALS ['padDataStore']    [$item] ) ) return 'data';
    elseif ( isset            ( $GLOBALS ['padBoolStore']    [$item] ) ) return 'bool';
    elseif ( isset            ( $GLOBALS ['padTables']       [$item] ) ) return 'table';
    elseif ( isset            ( $GLOBALS ['pqStore'] [$item]     ) ) return 'pull';
    elseif ( file_exists      ( "tag/$item.php"                      ) ) return 'tag';
    elseif ( padDataFileName  ( $item                                ) ) return 'local';    
    elseif ( padArrayCheck    ( $item, 1                             ) ) return 'array';
    elseif ( padFieldCheck    ( $item, 1                             ) ) return 'field';
    elseif ( padInclFileName  ( $item                                ) ) return 'include';
    elseif ( defined          ( $item                                ) ) return 'constant';
    elseif ( file_exists      ( APP . "_functions/$item.php"         ) ) return 'function';
    elseif ( file_exists      ( "functions/$item.php"                ) ) return 'function';
    elseif ( function_exists  ( $item                                ) ) return 'php';
    elseif ( file_exists      ( "sequence/types/$item"               ) ) return 'sequence';
    elseif ( file_exists      ( "sequence/actions/types/$item.php"   ) ) return 'action';
    else                                                                 return FALSE;

  }


  function padTypeSeq ( $type, $item ) {

    $GLOBALS ['padTypeSeq'] = $type;

    if ( $type == 'action' and file_exists ( "sequence/actions/types/$item.php" )                        ) return 'action';
    if ( $item == 'action' and file_exists ( "sequence/actions/types/$type.php" )                        ) return 'action';
        
    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ("sequence/actions/types/$item.php") ) return 'action';
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ("sequence/actions/types/$type.php") ) return 'action';

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ("sequence/types/$item")             ) return 'make';
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ("sequence/types/$type")             ) return 'make'; 

    if ( isset ( $GLOBALS ['pqStore'] [$type] ) and file_exists ("sequence/start/types/$item.php")   ) return $item;
    if ( isset ( $GLOBALS ['pqStore'] [$item] ) and file_exists ("sequence/start/types/$type.php")   ) return $type;

    if ( file_exists ( "sequence/types/$item" )    and file_exists ("sequence/start/types/$type.php")    ) return $type;
    if ( file_exists ( "sequence/types/$type" )    and file_exists ("sequence/start/types/$item.php")    ) return $item; 

    if ( file_exists ( "sequence/actions/types/$item.php" ) and file_exists ("sequence/start/types/$type.php") ) return $type;
    if ( file_exists ( "sequence/actions/types/$type.php" ) and file_exists ("sequence/start/types/$item.php") ) return $item; 

    return FALSE;

  }



  function padTypeCheck ( $type, $item ) {

    if     ( ! padValidType                          ( $type                                ) ) return FALSE;
    elseif (                       ! file_exists     ( "types/$type.php"                    ) ) return FALSE;
    elseif ( $type == 'app'      and file_exists     ( APP . "_tags/$item.php"              ) ) return $type;
    elseif ( $type == 'app'      and file_exists     ( APP . "_tags/$item.pad"              ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( "tags/$item.php"                     ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( "tags/$item.pad"                     ) ) return $type;
    elseif ( $type == 'tag'      and file_exists     ( "tag/$type.php"                      ) ) return $type;
    elseif ( $type == 'level'    and padChkLevel     ( $item                                ) ) return $type;
    elseif ( $type == 'bool'     and isset           ( $GLOBALS ['padBoolStore'] [$item]    ) ) return $type;
    elseif ( $type == 'content'  and isset           ( $GLOBALS ['padContentStore'] [$item] ) ) return $type;
    elseif ( $type == 'defined'  and padContentCheck ( $item                                ) ) return $type;
    elseif ( $type == 'data'     and isset           ( $GLOBALS ['padDataStore'] [$item]    ) ) return $type;
    elseif ( $type == 'local'    and padDataFileName ( $item                                ) ) return $type;
    elseif ( $type == 'include'  and padInclFileName ( $item                                ) ) return $type;
    elseif ( $type == 'array'    and padArrayCheck   ( $item, 1                             ) ) return $type;
    elseif ( $type == 'field'    and padFieldCheck   ( $item, 1                             ) ) return $type;
    elseif ( $type == 'constant' and defined         ( $item                                ) ) return $type;
    elseif ( $type == 'function' and file_exists     ( APP . "_functions/$item.php"         ) ) return $type;
    elseif ( $type == 'function' and file_exists     ( "functions/$item.php"                ) ) return $type;
    elseif ( $type == 'php'      and function_exists ( $item                                ) ) return $type;
    elseif ( $type == 'table'    and isset           ( $GLOBALS ['padTables'] [$item]       ) ) return $type;
    else                                                                                        return FALSE;

  }

  
?>