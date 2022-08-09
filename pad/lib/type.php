<?php

  function pGet_type_lvl ( $type ) {

    if     ( ! pValid    ( $type ) )                                       return FALSE;
    elseif ( file_exists     ( APP . "tags/$type.php"                 ) ) return 'tag_app';
    elseif ( file_exists     ( APP . "tags/$type.html"                ) ) return 'tag_app';
    elseif ( file_exists     ( PAD . "tags/$type.php"                 ) ) return 'tag_pad';
    elseif ( file_exists     ( PAD . "tags/$type.html"                ) ) return 'tag_pad';
    elseif ( file_exists     ( PAD . "tag/$type.php"                  ) ) return 'parm';
    elseif ( isset               ( $GLOBALS['pFlagStore'] [$type]     ) ) return 'flag';
    elseif ( isset               ( $GLOBALS['pContentStore'] [$type]  ) ) return 'content';
    elseif ( isset               ( $GLOBALS['pDataStore'] [$type]     ) ) return 'data';
    elseif ( file_exists     ( PAD . "sequence/types/$type"           ) ) return 'sequence';
    elseif ( file_exists     ( PAD . "sequence/actions/$type.php"     ) ) return 'action';
    elseif ( isset               ( $GLOBALS['pSequenceStore'] [$type] ) ) return 'store';
    elseif ( isset               ( $GLOBALS['pDbTables'] [$type]      ) ) return 'table';
    elseif ( pArray_check     ( $type                                  ) ) return 'array';
    elseif ( pField_check     ( $type                                  ) ) return 'field';
    elseif ( defined             ( $type                                  ) ) return 'constant';
    elseif ( file_exists     ( APP  . "functions/$type.php"           ) ) return 'function_app';
    elseif ( file_exists     ( PAD . "functions/$type.php"            ) ) return 'function_pad';
    elseif ( function_exists     ( $type                                  ) ) return 'function_php';
    elseif ( pChk_level_array ( $type                                  ) ) return 'level';
    elseif ( pIs_object       ( $type                                  ) ) return 'object';
    elseif ( pIs_resource     ( $type                                  ) ) return 'resource';
    else                                                                      return FALSE;

  }

  function pCheck_type ( $type, $name ) {

        if ( ! pValid ( $type ) or ! pValid ( $name)  )                                            return FALSE;
    elseif ( pChk_level_array ( $name                               ) and $type == 'level'          ) return TRUE;
    elseif ( file_exists  ( APP . "tags/$name.php"                 ) and $type == 'tag_app'        ) return TRUE;
    elseif ( file_exists  ( APP . "tags/$name.html"                ) and $type == 'tag_app'        ) return TRUE;
    elseif ( file_exists  ( PAD . "tags/$name.php"                 ) and $type == 'tag_pad'        ) return TRUE;
    elseif ( file_exists  ( PAD . "tags/$name.html"                ) and $type == 'tag_pad'        ) return TRUE;
    elseif ( file_exists  ( APP . "tags/$name.php"                 ) and $type == 'app'            ) return TRUE;
    elseif ( file_exists  ( APP . "tags/$name.html"                ) and $type == 'app'            ) return TRUE;
    elseif ( file_exists  ( PAD . "tags/$name.php"                 ) and $type == 'pad'            ) return TRUE;
    elseif ( file_exists  ( PAD . "tags/$name.html"                ) and $type == 'pad'            ) return TRUE;
    elseif ( file_exists  ( APP . "tags/$name.php"                 ) and $type == 'tag'            ) return TRUE;
    elseif ( file_exists  ( APP . "tags/$name.html"                ) and $type == 'tag'            ) return TRUE;
    elseif ( file_exists  ( PAD . "tags/$name.php"                 ) and $type == 'tag'            ) return TRUE;
    elseif ( file_exists  ( PAD . "tags/$name.html"                ) and $type == 'tag'            ) return TRUE;
    elseif ( file_exists  ( PAD . "tag/$name.php"                  ) and $type == 'parm'           ) return TRUE;
    elseif ( isset            ( $GLOBALS['pFlagStore'] [$name]     ) and $type == 'flag'           ) return TRUE;
    elseif ( isset            ( $GLOBALS['pContentStore'] [$name]  ) and $type == 'content'        ) return TRUE;
    elseif ( isset            ( $GLOBALS['pDataStore'] [$name]     ) and $type == 'data'           ) return TRUE;
    elseif ( file_exists  ( PAD . "sequence/types/$type"           ) and $type == 'sequence'       ) return TRUE;
    elseif ( file_exists  ( PAD . "sequence/actions/$type.php"     ) and $type == 'action'         ) return TRUE;
    elseif ( isset            ( $GLOBALS['pSequenceStore'] [$name] ) and $type == 'store'          ) return TRUE;
    elseif ( isset            ( $GLOBALS['pDbTables'] [$name]      ) and $type == 'table'          ) return TRUE;
    elseif ( pArray_check  ( $name                                  ) and $type == 'array'          ) return TRUE;
    elseif ( pField_check  ( $name                                  ) and $type == 'field'          ) return TRUE;
    elseif ( defined          ( $name                                  ) and $type == 'constant'       ) return TRUE;
    elseif ( file_exists  ( APP  . "functions/$name.php"           ) and $type == 'function_app'   ) return TRUE;
    elseif ( file_exists  ( PAD . "functions/$name.php"            ) and $type == 'function_pad'   ) return TRUE;
    elseif ( function_exists  ( $name                                  ) and $type == 'function_php'   ) return TRUE;
    elseif ( file_exists  ( APP  . "functions/$name.php"           ) and $type == 'function'       ) return TRUE;
    elseif ( file_exists  ( PAD . "functions/$name.php"            ) and $type == 'function'       ) return TRUE;
    elseif ( function_exists  ( $name                                  ) and $type == 'function'       ) return TRUE;
    elseif ( pIs_object    ( $name                                  ) and $type == 'object'         ) return TRUE;
    elseif ( pIs_resource  ( $type                                  ) and $type == 'resource'       ) return TRUE;
    else                                                                                                 return FALSE;

  }

  function pGet_type_eval ( $type ) {

        if ( ! pValid         ( $type                                  ) ) return FALSE;
    elseif ( file_exists         ( APP . "functions/$type.php"            ) ) return 'app';
    elseif ( file_exists         ( PAD . "functions/$type.php"            ) ) return 'pad';
    elseif ( function_exists     ( $type                                  ) ) return 'php';
    elseif ( pField_check     ( $type                                  ) ) return 'field';
    elseif ( isset               ( $GLOBALS['pFlagStore'] [$type]     ) ) return 'flag';
    elseif ( isset               ( $GLOBALS['pContentStore'] [$type]  ) ) return 'content';
    elseif ( isset               ( $GLOBALS['pDataStore'] [$type]     ) ) return 'data';
    elseif ( isset               ( $GLOBALS['pSequenceStore'] [$type] ) ) return 'sequence';
    elseif ( file_exists         ( PAD . "tag/$type.php"                  ) ) return 'parm';
    elseif ( isset               ( $GLOBALS['pDbTables'] [$type]      ) ) return 'table';
    elseif ( pArray_check     ( $type                                  ) ) return 'array';
    elseif ( pChk_level_array ( $type                                  ) ) return 'level';
    elseif ( defined             ( $type                                  ) ) return 'constant';
    elseif ( file_exists         ( PAD . "sequence/actions/$type.php"     ) ) return 'action';
    elseif ( file_exists         ( APP . "tags/$type.php"                 ) ) return 'tag_app';
    elseif ( file_exists         ( APP . "tags/$type.html"                ) ) return 'tag_app';
    elseif ( file_exists         ( PAD . "tags/$type.php"                 ) ) return 'tag_pad';
    elseif ( file_exists         ( PAD . "tags/$type.html"                ) ) return 'tag_pad';
    elseif ( pIs_object       ( $type                                  ) ) return 'object';
    elseif ( pIs_resource     ( $type                                  ) ) return 'resource';
    else                                                                      return FALSE;

  } 
  
?>