<?php
    
  if ( $pad_restart )
    return include PAD . 'inits/restart.php';    
    
  $pad_end [$pad_lvl] = strpos($pad_html[$pad_lvl], '}');

  if ( $pad_end[$pad_lvl] === FALSE )
    return include 'end.php';

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

  include 'parms1.php';

  if     ( $pad_first == '!' ) return pad_html ( include PAD . 'var/raw.php' );
  elseif ( $pad_first == '$' ) return pad_html ( include PAD . 'var/opt.php' );

  if     ( ! ctype_alpha ( $pad_first )  ) return pad_ignore ('ctype_alpha');
  elseif ( ! pad_valid   ( $pad_tag )    ) return pad_ignore ('pad_valid');

  $pad_tag_type = include 'type_get.php';

  if ( $pad_tag_type === FALSE )
    return pad_ignore ('tag_type_false');

  $pad_content = $pad_false = '';

  if ( $pad_pair ) {
    $pad_pair_result = include 'pair.php';
    if ( $pad_pair_result === FALSE ) 
      return pad_ignore ('pair_result_is_false');
  }

  include 'start.php';

?>