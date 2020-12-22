<?php

  if ( $pad_next )
    include PAD_HOME . 'build/build.php';
    
  $pad_end [$pad_lvl] = strpos($pad_html[$pad_lvl], '}');

  if ( $pad_end[$pad_lvl] === FALSE )
    return include PAD_HOME . 'level/end.php';

  $pad_start [$pad_lvl] = strrpos ( $pad_html[$pad_lvl], '{', $pad_end[$pad_lvl] - strlen($pad_html[$pad_lvl]) );
  
  if ( $pad_start [$pad_lvl] === FALSE ) {
    $pad_html [$pad_lvl] = substr($pad_html[$pad_lvl], 0, $pad_end[$pad_lvl])
                         . '&close;'
                         . substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1);
    return;
  }

  $pad_pair = ! ( substr($pad_html[$pad_lvl],$pad_end[$pad_lvl]-1,1) == '/' );

  if ( $pad_pair )
    $pad_between = substr($pad_html[$pad_lvl], $pad_start[$pad_lvl]+1, $pad_end[$pad_lvl]-$pad_start[$pad_lvl]-1);
  else
    $pad_between = substr($pad_html[$pad_lvl], $pad_start[$pad_lvl]+1, $pad_end[$pad_lvl]-$pad_start[$pad_lvl]-2);

  $pad_first   = substr($pad_between, 0, 1);
  $pad_words   = preg_split("/[\s]+/", $pad_between, 2, PREG_SPLIT_NO_EMPTY);
  $pad_tag     = trim($pad_words[0] ?? '');
  $pad_parms   = trim($pad_words[1] ?? '');

  pad_trace ('tag/inits', "tag=$pad_tag parms=$pad_parms pair=$pad_pair");
  
  if     ( $pad_first == '!'             ) return pad_html ( include PAD_HOME . 'level/var.php'  );
  elseif ( $pad_first == '$'             ) return pad_html ( include PAD_HOME . 'level/var.php'  );
  elseif ( ! ctype_alpha ( $pad_first )  ) return pad_ignore ();
  elseif ( ! pad_valid_name ( $pad_tag ) ) return pad_ignore ();

  $pad_pair_search = $pad_tag;
  $pad_parms_type  = ( $pad_parms ) ? 'open' : 'none';

  $pad_parameters [$pad_lvl+1] ['name'] = $pad_tag;
  $pad_parameters [$pad_lvl+1] ['parm'] = $pad_parms;

  $pad_ns_pos = strpos($pad_tag, ':');

  if ($pad_ns_pos) {

    $pad_tag_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag      = substr ($pad_tag, $pad_ns_pos+1);

    if ( ! file_exists ( PAD_HOME . "types/$pad_tag_type.php" ) ) 
      return pad_ignore ();
    
  } else {

    if     ( file_exists     ( PAD_APP  . "tags/$pad_tag.php"      ) ) $pad_tag_type = 'tag';
    elseif ( file_exists     ( PAD_HOME . "tags/$pad_tag.php"      ) ) $pad_tag_type = 'tag';
    elseif ( file_exists     ( PAD_HOME . "tag/$pad_tag.php"       ) ) $pad_tag_type = 'parm';
    elseif ( pad_level_array ( $pad_tag                            ) ) $pad_tag_type = 'level';
    elseif ( isset           ( $pad_flag_store [$pad_tag]          ) ) $pad_tag_type = 'flag';
    elseif ( isset           ( $pad_content_store [$pad_tag]       ) ) $pad_tag_type = 'content';
    elseif ( isset           ( $pad_data_store [$pad_tag]          ) ) $pad_tag_type = 'data';
    elseif ( isset           ( $pad_db_tables [$pad_tag]           ) ) $pad_tag_type = 'table';
    elseif ( file_exists     ( PAD_APP  . "functions/$pad_tag.php" ) ) $pad_tag_type = 'function';
    elseif ( file_exists     ( PAD_HOME . "functions/$pad_tag.php" ) ) $pad_tag_type = 'function';
    elseif ( pad_array_check ( $pad_tag                            ) ) $pad_tag_type = 'array';
    elseif ( pad_field_check ( $pad_tag                            ) ) $pad_tag_type = 'field';
    elseif ( defined         ( $pad_tag                            ) ) $pad_tag_type = 'constant';
    elseif ( function_exists ( $pad_tag                            ) ) $pad_tag_type = 'php';
    elseif ( pad_is_object   ( $pad_tag                            ) ) $pad_tag_type = 'object';
    elseif ( pad_is_resource ( $pad_tag                            ) ) $pad_tag_type = 'resource';
    else                                                               return pad_ignore ();

  }

  $pad_content = $pad_false = '';

  if ( $pad_pair ) {
    $pad_pair_result = include PAD_HOME . 'level/pair.php';
    if ( $pad_pair_result === FALSE ) 
      return pad_ignore ();
  }

  include PAD_HOME . 'level/start.php';

?>