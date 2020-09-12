<?php

  if ( $pad_next )
    include PAD_HOME . 'build/build.php';
    
  $pad_end [$pad_lvl] = strpos($pad_html[$pad_lvl], '}');

  if ( $pad_end[$pad_lvl] === FALSE ) {
    
    if ( count ($pad_data[$pad_lvl] ) )
      include PAD_HOME . 'occurrence/end.php';

    if ( next($pad_data[$pad_lvl]) !== FALSE )
      include PAD_HOME . 'occurrence/start.php';
    else
      include PAD_HOME . 'level/end.php';

    return;

  }

  $pad_start [$pad_lvl] = strrpos ($pad_html[$pad_lvl], '{', $pad_end[$pad_lvl] - strlen($pad_html[$pad_lvl]));
  
  if ( $pad_start [$pad_lvl] === FALSE ) {
    $pad_html [$pad_lvl] = substr($pad_html[$pad_lvl], 0, $pad_end[$pad_lvl])
                         . '&close;'
                         . substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1);
    return;
  }

  $pad_between = substr($pad_html[$pad_lvl], $pad_start[$pad_lvl]+1, $pad_end[$pad_lvl]-$pad_start[$pad_lvl]-1);
  $pad_char    = substr($pad_between, 0, 1);
  
  if     ( $pad_char == '!' )             return pad_html ( pad_escape ( pad_field_value (substr($pad_between,1),1) ) );
  elseif ( $pad_char == '$' )             return pad_html ( include PAD_HOME . 'level/var.php' );
  elseif ( ! pad_valid_name($pad_char) )  return pad_html ( '&open;' . $pad_between . '&close;' );

  $pad_words       = preg_split("/[\s]+/", $pad_between, 2, PREG_SPLIT_NO_EMPTY);
  $pad_tag         = trim($pad_words[0] ?? '');
  $pad_parms       = trim($pad_words[1] ?? '');
  $pad_pair_search = $pad_tag;
  $pad_parms_type  = ( $pad_parms ) ? 'open' : 'none';

  $pad_parameters [$pad_lvl+1] ['name'] = $pad_tag;
  $pad_parameters [$pad_lvl+1] ['parm'] = $pad_parms;

  $pad_ns_pos = strpos($pad_tag, ':');

  if ($pad_ns_pos) {

    $pad_tag_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag      = substr ($pad_tag, $pad_ns_pos+1);

  } else {

    if     ( file_exists     ( PAD_APP  . "tags/$pad_tag.php" ) ) $pad_tag_type = 'app';
    elseif ( file_exists     ( PAD_HOME . "tags/$pad_tag.php" ) ) $pad_tag_type = 'tag';
    elseif ( pad_level_array ( $pad_tag                       ) ) $pad_tag_type = 'level';
    elseif ( isset           ( $pad_content_store [$pad_tag]  ) ) $pad_tag_type = 'content';
    elseif ( isset           ( $pad_data_store [$pad_tag]     ) ) $pad_tag_type = 'data';
    elseif ( isset           ( $pad_db_tables [$pad_tag]      ) ) $pad_tag_type = 'table';
    elseif ( pad_array_check ( $pad_tag                       ) ) $pad_tag_type = 'array';
    elseif ( pad_field_check ( $pad_tag                       ) ) $pad_tag_type = 'field';
    elseif ( defined         ( $pad_tag                       ) ) $pad_tag_type = 'constant';
    elseif ( function_exists ( $pad_tag                       ) ) $pad_tag_type = 'function';
    elseif ( is_object       ( $pad_tag                       ) ) $pad_tag_type = 'object';
    elseif ( is_resource     ( $pad_tag                       ) ) $pad_tag_type = 'resource';
    else                                                          return pad_html ( '&open;' . $pad_between . '&close;' );

  }
 
  include PAD_HOME . 'level/start.php';
 
?>