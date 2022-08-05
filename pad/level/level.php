<?php
    
  if ( $pad_restart )
    return include PAD . 'inits/restart.php';    
    
  $pad_end [$pad] = strpos($pad_html[$pad], '}');

  if ( $pad_end[$pad] === FALSE )
    return include 'end.php';

  $pad_start [$pad] = strrpos ( $pad_html[$pad], '{', $pad_end[$pad] - strlen($pad_html[$pad]) );
  
  if ( $pad_start [$pad] === FALSE ) {
    $pad_html [$pad] = substr($pad_html[$pad], 0, $pad_end[$pad])
                         . '&close;'
                         . substr($pad_html[$pad], $pad_end[$pad]+1);
    return;
  }

  $pad_pair = ! ( substr($pad_html[$pad],$pad_end[$pad]-1,1) == '/' );

  if ( $pad_pair )
    $pad_between = substr($pad_html[$pad], $pad_start[$pad]+1, $pad_end[$pad]-$pad_start[$pad]-1);
  else
    $pad_between = substr($pad_html[$pad], $pad_start[$pad]+1, $pad_end[$pad]-$pad_start[$pad]-2);

  $pad++;
  include 'setup.php';
  $pad--;

  if     ( $pad_first == '!' ) return pad_html ( include PAD . 'var/raw.php' );
  elseif ( $pad_first == '$' ) return pad_html ( include PAD . 'var/opt.php' );

  if     ( ! ctype_alpha ( $pad_first )  ) return pad_ignore ('ctype_alpha');
  elseif ( ! pad_valid   ( $pad_tag )    ) return pad_ignore ('pad_valid');

  $pad_type = include 'type_get.php';

  if ( $pad_type === FALSE )
    return pad_ignore ('type_false');

  $pad_content [$pad+1] = $pad_false [$pad+1] = '';

  if ( $pad_pair ) {
    $pad_pair_result = include 'pair.php';
    if ( $pad_pair_result === FALSE ) 
      return pad_ignore ('pair_result_is_false');
  }

  include 'start.php';

?>