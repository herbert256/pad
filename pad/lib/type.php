<?php

  function pad_get_type_lvl ( $type ) {

    if     ( ! pad_valid    ( $type ) )                                 return FALSE;
    elseif ( pad_file_exists     ( APP . "tags/$type.php"                ) ) return 'tag_app';
    elseif ( pad_file_exists     ( APP . "tags/$type.html"               ) ) return 'tag_app';
    elseif ( pad_file_exists     ( PAD . "tags/$type.php"                ) ) return 'tag_pad';
    elseif ( pad_file_exists     ( PAD . "tags/$type.html"               ) ) return 'tag_pad';
    elseif ( pad_chk_level_array ( $type                                 ) ) return 'level';
    elseif ( pad_file_exists     ( PAD . "tag/$type.php"                 ) ) return 'parm';
    elseif ( isset               ( $GLOBALS['pad_flag_store'] [$type]    ) ) return 'flag';
    elseif ( isset               ( $GLOBALS['pad_content_store'] [$type] ) ) return 'content';
    elseif ( isset               ( $GLOBALS['pad_data_store'] [$type]    ) ) return 'data';
    elseif ( isset               ( $GLOBALS['pad_seq_store'] [$type]     ) ) return 'store';
    elseif ( pad_file_exists     ( PAD . "sequence/types/$type"          ) ) return 'sequence';
    elseif ( isset               ( $GLOBALS['pad_db_tables'] [$type]     ) ) return 'table';
    elseif ( pad_array_check     ( $type                                 ) ) return 'array';
    elseif ( pad_field_check     ( $type                                 ) ) return 'field';
    elseif ( defined             ( $type                                 ) ) return 'constant';
    elseif ( pad_file_exists     ( APP  . "functions/$type.php"          ) ) return 'function_app';
    elseif ( pad_file_exists     ( PAD . "functions/$type.php"           ) ) return 'function_pad';
    elseif ( function_exists     ( $type                                 ) ) return 'function_php';
    elseif ( pad_is_object       ( $type                                 ) ) return 'object';
    elseif ( pad_is_resource     ( $type                                 ) ) return 'resource';
    else                                                                     return FALSE;

  }

  function pad_check_type ( $type, $name ) {

        if ( ! pad_valid ( $type ) or ! pad_valid ( $name)  )                                           return FALSE;
    elseif ( pad_chk_level_array ( $name                             ) and $type == 'level'          ) return TRUE;
    elseif ( pad_file_exists     ( APP . "tags/$name.php"                ) and $type == 'tag_app'        ) return TRUE;
    elseif ( pad_file_exists  ( APP . "tags/$name.html"               ) and $type == 'tag_app'        ) return TRUE;
    elseif ( pad_file_exists  ( PAD . "tags/$name.php"                ) and $type == 'tag_pad'        ) return TRUE;
    elseif ( pad_file_exists  ( PAD . "tags/$name.html"               ) and $type == 'tag_pad'        ) return TRUE;
    elseif ( pad_file_exists  ( PAD . "tag/$name.php"                 ) and $type == 'parm'           ) return TRUE;
    elseif ( isset            ( $GLOBALS['pad_flag_store'] [$name]    ) and $type == 'flag'           ) return TRUE;
    elseif ( isset            ( $GLOBALS['pad_content_store'] [$name] ) and $type == 'content'        ) return TRUE;
    elseif ( isset            ( $GLOBALS['pad_data_store'] [$name]    ) and $type == 'data'           ) return TRUE;
    elseif ( isset            ( $GLOBALS['pad_seq_store'] [$name]     ) and $type == 'store'          ) return TRUE;
    elseif ( pad_file_exists  ( PAD . "sequence/types/$type"          ) and $type == 'sequence'       ) return TRUE;
    elseif ( isset            ( $GLOBALS['pad_db_tables'] [$name]     ) and $type == 'table'          ) return TRUE;
    elseif ( pad_array_check  ( $name                                 ) and $type == 'array'          ) return TRUE;
    elseif ( pad_field_check  ( $name                                 ) and $type == 'field'          ) return TRUE;
    elseif ( defined          ( $name                                 ) and $type == 'constant'       ) return TRUE;
    elseif ( pad_file_exists  ( APP  . "functions/$name.php"          ) and $type == 'function_app'   ) return TRUE;
    elseif ( pad_file_exists  ( PAD . "functions/$name.php"           ) and $type == 'function_pad'   ) return TRUE;
    elseif ( function_exists  ( $name                                 ) and $type == 'function_php'   ) return TRUE;
    elseif ( pad_is_object    ( $name                                 ) and $type == 'object'         ) return TRUE;
    elseif ( pad_is_resource  ( $type                                 ) and $type == 'resource'       ) return TRUE;
    else                                                                                                return FALSE;

  }

  function pad_get_type_eval ( $type ) {

        if ( ! pad_valid         ( $type                                 ) ) return FALSE;
    elseif ( pad_file_exists     ( APP . "functions/$type.php"           ) ) return 'function_app';
    elseif ( pad_file_exists     ( PAD . "functions/$type.php"           ) ) return 'function_pad';
    elseif ( function_exists     ( $type                                 ) ) return 'function_php';
    elseif ( pad_field_check     ( $type                                 ) ) return 'field';
    elseif ( isset               ( $GLOBALS['pad_flag_store'] [$type]    ) ) return 'flag';
    elseif ( isset               ( $GLOBALS['pad_content_store'] [$type] ) ) return 'content';
    elseif ( isset               ( $GLOBALS['pad_data_store'] [$type]    ) ) return 'data';
    elseif ( isset               ( $GLOBALS['pad_seq_store'] [$type]     ) ) return 'store';
    elseif ( pad_file_exists     ( PAD . "tag/$type.php"                 ) ) return 'parm';
    elseif ( isset               ( $GLOBALS['pad_db_tables'] [$type]     ) ) return 'table';
    elseif ( pad_array_check     ( $type                                 ) ) return 'array';
    elseif ( pad_chk_level_array ( $type                                 ) ) return 'level';
    elseif ( defined             ( $type                                 ) ) return 'constant';
    elseif ( pad_file_exists     ( APP . "tags/$type.php"                ) ) return 'tag_app';
    elseif ( pad_file_exists     ( APP . "tags/$type.html"               ) ) return 'tag_app';
    elseif ( pad_file_exists     ( PAD . "tags/$type.php"                ) ) return 'tag_pad';
    elseif ( pad_file_exists     ( PAD . "tags/$type.html"               ) ) return 'tag_pad';
    elseif ( pad_is_object       ( $type                                 ) ) return 'object';
    elseif ( pad_is_resource     ( $type                                 ) ) return 'resource';
    else                                                                     return FALSE;

  } 
  
?>