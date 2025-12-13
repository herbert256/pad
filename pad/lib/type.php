<?php


  /**
   * Determines the common type of a tag name.
   *
   * Checks various type stores and file locations to classify
   * the tag: pull, flag, content, data, include, field, property,
   * array, parm, level, constant, local, script, php, page,
   * sequence, or action.
   *
   * @param string $type The type name to classify.
   *
   * @return string|false The type classification or FALSE if unknown.
   */
  function padTypeCommon ( $type ) {

    if     ( isset              ( $GLOBALS ['pqStore']         [$type] ) ) return 'pull';
    elseif ( isset              ( $GLOBALS ['padBoolStore']    [$type] ) ) return 'flag';
    elseif ( isset              ( $GLOBALS ['padContentStore'] [$type] ) ) return 'content';
    elseif ( isset              ( $GLOBALS ['padDataStore']    [$type] ) ) return 'data';
    elseif ( padAppIncludeCheck ( $type                                ) ) return 'include';   
    elseif ( padFieldCheck      ( $type                                ) ) return 'field';
    elseif ( padTagCheck        ( $type                                ) ) return 'property';
    elseif ( padArrayCheck      ( $type                                ) ) return 'array';
    elseif ( padOptCheck        ( $type, 1                             ) ) return 'parm';
    elseif ( padChkLevel        ( $type                                ) ) return 'level';
    elseif ( defined            ( $type                                ) ) return 'constant';
    elseif ( padDataFileName    ( $type                                ) ) return 'local';      
    elseif ( padScriptCheck     ( $type                                ) ) return 'script';
    elseif ( function_exists    ( $type                                ) ) return 'php';
    elseif ( padAppPageCheck    ( $type                                ) ) return 'page';   
    elseif ( file_exists        ( PT . $type                           ) ) return 'sequence';
    elseif ( file_exists        ( PQ . "actions/types/$type.php"       ) ) return 'action';

    return FALSE;    
    
  } 


  /**
   * Determines the type of a tag.
   *
   * Checks app tags, pad tags, common types, and optionally
   * function types.
   *
   * @param string $type       The tag name to check.
   * @param int    $goFunction Also check function types (default 1).
   *
   * @return string|false The tag type or FALSE if not found.
   */
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


  /**
   * Validates a type:item combination.
   *
   * Checks if the specified type handler exists and the item
   * matches that type classification.
   *
   * @param string $type The type name (e.g., 'app', 'pad', 'function').
   * @param string $item The item name to validate against the type.
   *
   * @return string|false The type if valid, FALSE otherwise.
   */
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


  /**
   * Determines the type of a pipe function.
   *
   * Checks app functions, pad functions, common types, and
   * optionally falls back to tag types.
   *
   * @param string $type  The function name to check.
   * @param int    $goTag Also check tag types (default 1).
   *
   * @return string|false The function type or FALSE if not found.
   */
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



  /**
   * Determines sequence type from type:item pair.
   *
   * Resolves sequence actions and start types based on file
   * existence and store contents.
   *
   * @param string $type The first part of type:item.
   * @param string $item The second part of type:item.
   *
   * @return string|false The resolved sequence type or FALSE.
   *
   * @global string $padTypeSeq Stores the type for reference.
   */
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
