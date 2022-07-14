<?php

start:

  if ( $pad_next )
    include PAD . 'build/build.php';
    
  $pad_end [$pad_lvl] = strpos($pad_html[$pad_lvl], '}');

  if ( $pad_end[$pad_lvl] === FALSE )
    return include PAD . 'level/end.php';

  $pad_start [$pad_lvl] = strrpos ( $pad_html[$pad_lvl], '{', $pad_end[$pad_lvl] - strlen($pad_html[$pad_lvl]) );
  
  if ( $pad_start [$pad_lvl] === FALSE ) {
    $pad_html [$pad_lvl] = substr($pad_html[$pad_lvl], 0, $pad_end[$pad_lvl])
                         . '&close;'
                         . substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1);
    goto start;
  }

  $pad_pair = ! ( substr($pad_html[$pad_lvl],$pad_end[$pad_lvl]-1,1) == '/' );

  if ( $pad_pair )
    $pad_between = substr($pad_html[$pad_lvl], $pad_start[$pad_lvl]+1, $pad_end[$pad_lvl]-$pad_start[$pad_lvl]-1);
  else
    $pad_between = substr($pad_html[$pad_lvl], $pad_start[$pad_lvl]+1, $pad_end[$pad_lvl]-$pad_start[$pad_lvl]-2);

  include PAD . 'level/parms1.php';

  if     ( $pad_first == '!' ) { pad_html ( include PAD . 'var/raw.php' ); goto start; }
  elseif ( $pad_first == '$' ) { pad_html ( include PAD . 'var/opt.php' ); goto start; }

  if     ( ! ctype_alpha ( $pad_first )  ) { pad_ignore ('ctype_alpha'); goto start; }
  elseif ( ! pad_valid   ( $pad_tag )    ) { pad_ignore ('pad_valid');   goto start; }
  
  $pad_ns_pos = strpos($pad_tag, ':');

  if ( $pad_ns_pos ) {

    $pad_tag_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag      = substr ($pad_tag, $pad_ns_pos+1);

    if ( ! pad_file_exists ( PAD . "types/$pad_tag_type.php" ) ) 
      { pad_ignore ('tag_type_not_exists'); goto start; }
    
  } else {

    $pad_tag_type = pad_get_type_lvl ( $pad_tag );

    if ( $pad_tag_type === FALSE )
      { pad_ignore ('tag_type_false'); goto start; }

  }

  $pad_content = $pad_false = '';

  if ( $pad_pair ) {
    $pad_pair_result = include PAD . 'level/pair.php';
    if ( $pad_pair_result === FALSE ) 
      { pad_ignore ('pair_result_is_false'); goto start; }
  }

  include PAD . 'level/start.php';

  goto start;

?>