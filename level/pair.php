<?php

  $pad_content = $pad_false = '';
  $pad_pair    = $pad_single  = FALSE;

  $pad_pos = $pad_end [$pad_lvl];

  pad_trace('pair', "start: $pad_pair_search");

go2:  
  do {

    $pad_pos = strpos($pad_html[$pad_lvl] , '{/' . $pad_pair_search, $pad_pos);

    if ($pad_pos === FALSE) {
      pad_trace('pair', "result: SINGLE");
      $pad_single  = TRUE;
      $pad_content = '';
      return TRUE;
    } 

    $pad_content = substr($pad_html[$pad_lvl], $pad_end[$pad_lvl]+1, $pad_pos - $pad_end[$pad_lvl] - 1);

    $pad_pos++;

  } while ( ( substr_count($pad_content, '{'.$pad_pair_search.' ' ) + substr_count($pad_content, '{'.$pad_pair_search.'}' ) )
              <>    
            ( substr_count($pad_content, '{/'.$pad_pair_search.' ') + substr_count($pad_content, '{/'.$pad_pair_search.'}') ) );

  $pad_pair_check = $pad_html[$pad_lvl].' }|#';
  $pad_pair_mark  = strpos ( $pad_pair_check, '#', $pad_pos );
  $pad_pair_close = strpos ( $pad_pair_check, '}', $pad_pos );
  $pad_pair_space = strpos ( $pad_pair_check, ' ', $pad_pos );
  $pad_pair_pipe  = strpos ( $pad_pair_check, '|', $pad_pos );

  if ( $pad_pair_mark < $pad_pair_close and $pad_pair_mark < $pad_pair_space and $pad_pair_mark < $pad_pair_pipe ) {
    $pad_pos++;
    goto go2;
  }

  pad_trace('pair', "result: PAIR");
  
  $pad_pair    = TRUE;    
  $pad_content = substr ($pad_content, 0, $pad_pos);

  $pad_end [$pad_lvl] = strpos ( $pad_html[$pad_lvl], '}', $pad_pos+2);
  if ( $pad_end [$pad_lvl] === FALSE )
    pad_error ("No closure of close tag found");

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
      pad_error ("Both open and close parameters used: $pad_pair_search / $pad_parms / $pad_parms2");

    if ( strpos($pad_parms2, '}') ) 
       return include PAD_HOME . 'level/close.php';

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
    $pad_pos3 = strpos($pad_content, ' ', $pad_pos);
    if ($pad_pos3 !== FALSE and $pad_pos3 < $pad_pos2 )
      $pad_pos2 = $pad_pos3;      
    $pad_open_close [substr($pad_content, $pad_pos+2, $pad_pos2-$pad_pos-2)] = TRUE;
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

  foreach ( $pad_open_close as $pad_false_tag => $pad_dummy_var )
    if ( strpos($pad_false, "{/$pad_false_tag") and ! pad_check_tag ($pad_false_tag, $pad_false) )
      return pad_tag_error ('Number of {' . $pad_false_tag . '} and {/' . $pad_false_tag . '} do not match');
  
  foreach ( $pad_open_close as $pad_false_tag => $pad_dummy_var )
    if ( strpos($pad_content, "{/$pad_false_tag") and ! pad_check_tag ($pad_false_tag, $pad_content) )
      return pad_tag_error ('Number of {' . $pad_false_tag . '} and {/' . $pad_false_tag . '} do not match');

  return TRUE;

?>