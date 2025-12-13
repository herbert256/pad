<?php


  function padTypeCommon ( $type ) {

    if     ( isset              ( $GLOBALS ['pqStore']         [$type] ) ) return 'pull';
    elseif ( isset              ( $GLOBALS ['padBoolStore']    [$type] ) ) return 'flag';
    elseif ( isset              ( $GLOBALS ['padContentStore'] [$type] ) ) return 'content';
    elseif ( isset              ( $GLOBALS ['padDataStore']    [$type] ) ) return 'data';
    elseif ( padAppIncludeCheck ( $type                                ) ) return 'include';   
    elseif ( padAppPageCheck    ( $type                                ) ) return 'page';   
    elseif ( padFieldCheck      ( $type                                ) ) return 'field';
    elseif ( padTagCheck        ( $type                                ) ) return 'property';
    elseif ( padArrayCheck      ( $type                                ) ) return 'array';
    elseif ( padOptCheck        ( $type, 1                             ) ) return 'parm';
    elseif ( padChkLevel        ( $type                                ) ) return 'level';
    elseif ( defined            ( $type                                ) ) return 'constant';
    elseif ( padDataFileName    ( $type                                ) ) return 'local';      
    elseif ( padScriptCheck     ( $type                                ) ) return 'script';
    elseif ( function_exists    ( $type                                ) ) return 'php';
    elseif ( file_exists        ( PT . $type                           ) ) return 'sequence';
    elseif ( file_exists        ( PQ . "actions/types/$type.php"       ) ) return 'action';

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

    if     ( ! padValidType                          ( $type                   ) ) return FALSE;
    elseif (                       ! file_exists     ( PAD . "types/$type.php" ) ) return FALSE;
    elseif ( $type == 'app'      and padAppTagCheck  ( $item                   ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( PAD . "tags/$item.php"  ) ) return $type;
    elseif ( $type == 'pad'      and file_exists     ( PAD . "tags/$item.pad"  ) ) return $type;
    elseif ( padTypeCommon ( $item ) == $type                                    ) return $type;
    elseif ( $type == 'function' and padTypeFunction  ( $item, 0               ) ) return $type;
  
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
