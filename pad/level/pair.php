<?php

  $pad_pos = $pad_end [$pad_lvl];

go2:  
  do {

    $pad_pos = strpos($pad_html[$pad_lvl] , '{/' . $pad_pair_search, $pad_pos);

    if ($pad_pos === FALSE) {
      $pad_pair = FALSE;
      $pad_content = '';
      return TRUE;
    } 

    $pad_content = substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1, $pad_pos - $pad_end[$pad_lvl] - 1);

    $pad_pos++;

  } while ( substr_count($pad_content, '{'.$pad_pair_search ) <> substr_count($pad_content, '{/'.$pad_pair_search) );

  $pad_pair_check = substr($pad_html[$pad_lvl], $pad_pos + strlen($pad_pair_search) + 1, 1);
  if ( ! ($pad_pair_check == ' ' or $pad_pair_check == '}' or $pad_pair_check ==  ',') )
    goto go2;
 
  $pad_content = substr ($pad_content, 0, $pad_pos);

  $pad_end [$pad_lvl] = strpos ( $pad_html[$pad_lvl], '}', $pad_pos+2);
  if ( $pad_end [$pad_lvl] === FALSE )
    return pad_error ("No closure of close tag found");

  $pad_tmp = substr ($pad_html[$pad_lvl], $pad_pos+1, $pad_end[$pad_lvl]-$pad_pos-1);

  while ( substr_count($pad_tmp, '{') <> substr_count($pad_tmp, '}') ) {

    if ( $pad_end [$pad_lvl] === FALSE or $pad_end [$pad_lvl] + 1 == strlen($pad_html[$pad_lvl]) )
       break;

    $pad_end [$pad_lvl] = strpos ( $pad_html[$pad_lvl], '}', $pad_end [$pad_lvl] + 1); 
    if ( $pad_end [$pad_lvl] !== FALSE )
      $pad_tmp = substr ($pad_html[$pad_lvl], $pad_pos+1, $pad_end[$pad_lvl]-$pad_pos-1);

  }

  if ( $pad_end [$pad_lvl] === FALSE )
    $pad_end [$pad_lvl] = strpos ( $pad_html[$pad_lvl], '}', $pad_pos+2);

  $pad_between2 = substr ($pad_html[$pad_lvl], $pad_pos+1, $pad_end[$pad_lvl]-$pad_pos-1);
  $pad_words    = preg_split ("/[\s]+/", $pad_between2, 2, PREG_SPLIT_NO_EMPTY);
  $pad_parms2   = trim ($pad_words[1] ?? '');

  if ($pad_parms2) {

    if ($pad_parms) 
      return pad_error ("Both open and close parameters used: $pad_pair_search / $pad_parms / $pad_parms2");

    if ( strpos($pad_parms2, '}') ) {
      pad_html ( '{close_parms}' . $pad_parms2 . '{/close_parms}'
               . '{' . $pad_pair_search . '}'
               . $pad_content
               . '{/'. $pad_pair_search . ' ###%%%close_parms%%%###}') ;
      return TRUE;
    }

    $pad_between    = $pad_between2;
    $pad_parms      = $pad_parms2;
    $pad_parms_type = 'close';

    $pad_parameters [$pad_lvl+1] ['parm'] = $pad_parms;
 
  }

  $pad_open_close = [];
  $pad_open_close [$pad_pair_search] = TRUE;

  $pad_pos = strpos($pad_content, '{/', 0);

  while ($pad_pos !== FALSE) {
    $pad_pos2 = strpos($pad_content, '}', $pad_pos);
    if ( $pad_pos2 !== FALSE ) {
      $pad_pos3 = strpos($pad_content, ' ', $pad_pos);
      if ($pad_pos3 !== FALSE and $pad_pos3 < $pad_pos2 )
        $pad_pos2 = $pad_pos3;      
      $pad_false_tag = substr($pad_content, $pad_pos+2, $pad_pos2-$pad_pos-2);
      if ( pad_valid ($pad_false_tag) )
        $pad_open_close [$pad_false_tag] = TRUE;
    }
    $pad_pos = strpos($pad_content, '{/', $pad_pos+1);
  }

  $pad_pos = -1;

go: $pad_pos++;
    $pad_pos = strpos($pad_content, '{else}', $pad_pos);

  if ( $pad_pos === FALSE )
    return TRUE;
  
  $pad_false_check = substr($pad_content,0,$pad_pos);

  foreach ( $pad_open_close as $pad_false_tag => $pad_dummy_var )
    if ( ( substr_count($pad_false_check, '{'.$pad_false_tag.' ' ) + substr_count($pad_false_check, '{'.$pad_false_tag.'}' ) )
           <> 
         ( substr_count($pad_false_check, '{/'.$pad_false_tag.' ') + substr_count($pad_false_check, '{/'.$pad_false_tag.'}') ) )
      goto go;

  $pad_false   = substr($pad_content, $pad_pos+6);
  $pad_content = substr($pad_content, 0, $pad_pos);

  return TRUE;

?>