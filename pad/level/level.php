<?php

  if ( $pad_next )
    include PAD_HOME . 'pad/build/build.php';
    
  $pad_end [$pad_lvl] = strpos($pad_html[$pad_lvl], '}');

  if ( $pad_end[$pad_lvl] === FALSE )
    return include PAD_HOME . 'pad/level/end.php';

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

  if ( $pad_between == 'dump' )
    pad_dump ('{dump} tag found');

  include PAD_HOME . 'pad/level/parms1.php';

  if     ( $pad_first == '!' ) return pad_html ( include PAD_HOME . 'pad/level/var.php' );
  elseif ( $pad_first == '$' ) return pad_html ( include PAD_HOME . 'pad/level/var.php' );

  if     ( ! ctype_alpha ( $pad_first )  ) return pad_ignore ('ctype_alpha');
  elseif ( ! pad_valid_name ( $pad_tag ) ) return pad_ignore ('pad_valid_name');
  
  $pad_ns_pos = strpos($pad_tag, ':');

  if ( $pad_ns_pos ) {

    $pad_tag_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag      = substr ($pad_tag, $pad_ns_pos+1);

    if ( ! pad_file_exists ( PAD_HOME . "pad/types/$pad_tag_type.php" ) ) 
      return pad_ignore ('tag_type_not_exists');
    
  } else {

    $pad_tag_type = pad_get_type_lvl ( $pad_tag );

    if ( $pad_tag_type === FALSE )
      return pad_ignore ('tag_type_false');

  }

  $pad_content = $pad_false = '';

  if ( $pad_pair ) {
    $pad_pair_result = include PAD_HOME . 'pad/level/pair.php';
    if ( $pad_pair_result === FALSE ) 
      return pad_ignore ('pair_result_is_false');
  }

  return include PAD_HOME . 'pad/level/start.php';

?>