<?php
    
  function pad_colors_file ($file) {

    if ( ! file_exists($file) )
      return '';

    if (substr($file, -5) == '.html')
      return pad_colors_string ( file_get_contents($file) ) ;
    else
      return highlight_string ( file_get_contents($file), TRUE ) ;
  
  }  

  function pad_colors_string ($source) { 
    
    $source = highlight_string($source, TRUE);

go: $end = strpos($source, '}');

    if ( $end === FALSE )
      return $source;

    $start = strrpos ($source, '{', $end - strlen($source));
    
    if ( $start === FALSE ) {
      $source = substr($source, 0, $end)
              . '#close#'
              . substr($source, $end+1);
      goto go;
    }

    $between = substr($source, $start+1, $end-$start-1);
    $char    = substr($between, 0, 1);

    if ( $char == '$' or $char == '!' ) {
      $source = substr($source, 0, $start) 
              . '<b>#open#<font color="green">' 
              . $between 
              . '</font>#close#</b>' 
              . substr($source, $end+1);      
        goto go;
    }

    $check  = str_replace('&nbsp;',  ' ', $between);
    $words  = preg_split("/[\s]+/", $check, 2, PREG_SPLIT_NO_EMPTY);
    $tag    = trim($words[0] ?? '');
    $search = $tag;

    if ( $char == '/' )      
      goto co;

    $ns_pos = strpos($tag, ':');
    if ($ns_pos) {
      $tag_type = substr ($tag, 0, $ns_pos);
      $tag      = substr ($tag, $ns_pos+1);
    } 

    if ( ! pad_valid_name ( $tag ) )  {             
      $source = substr($source, 0, $start) 
                . '#open#' . $between . '#close#' 
                . substr($source, $end+1);
      goto go;
    } 

co: $parms  = $words[1] ?? '';

    $space  = ($parms) ? ' ' : '';
    $parms  = str_replace ('<b>#open#<font color="green">', '',  $parms);
    $parms  = str_replace ('</font>#close#</b>', '',             $parms);
    $parms  = str_replace (' ', '&nbsp;',                        $parms);
    $parms  = str_replace ('=', '<font color="black">=</font>',  $parms);
    $parms  = str_replace ('|', '<font color="black">|</font>',  $parms);
    $parms  = str_replace ('@', '<font color="black">@</font>',  $parms);
    $parms  = str_replace ('$', '<font color="black">$</font>',  $parms);

    $source = substr($source, 0, $start) 
            . '<b>#open#<font color="blue">'.$search.$space.'</font><font color="red">' 
            . $parms
            . '</font>#close#</b>' 
            . substr($source, $end+1);

    goto go;

  }

?>