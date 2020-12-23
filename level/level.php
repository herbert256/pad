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

  if ( $pad_ns_pos ) {

    $pad_tag_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag      = substr ($pad_tag, $pad_ns_pos+1);

    if ( ! file_exists ( PAD_HOME . "types/$pad_tag_type.php" ) ) 
      return pad_ignore ();
    
  } else {

    $pad_tag_type = pad_get_type ( $pad_tag );

    if ( $pad_tag_type === FALSE )
      return pad_ignore ();

  }

  $pad_content = $pad_false = '';

  if ( $pad_pair ) {
    $pad_pair_result = include PAD_HOME . 'level/pair.php';
    if ( $pad_pair_result === FALSE ) 
      return pad_ignore ();
  }

  include PAD_HOME . 'level/start.php';

?>