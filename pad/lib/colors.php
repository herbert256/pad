<?php
    
  function pad_colors_file ($file) {

    if ( ! pad_file_exists($file) )
      return 'xx';

    if (substr($file, -5) == '.html')
      return pad_colors_string ( pad_file_get_contents($file) ) ;
    else
      return highlight_string ( pad_file_get_contents($file), TRUE ) ;
  
  }  

  function pad_colors_string ($source) { 
    
   $source = highlight_string($source, TRUE);
   $source = str_replace ('@content@', '<font color="black"><b><font color="green">@content@</font></b></font>',  $source);

go: $end = strpos($source, '}');

   $souce = str_replace (',', '<font color="black">,</font>',  $source);

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

     $between = str_replace ('#', '<font color="black"><b><font color="black">#</font></b></font>',  $between);

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

    if ( ! pad_valid ( $tag ) )  {             
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
    $parms  = str_replace (',', '<font color="black">,</font>',  $parms);

    if (substr($parms, -1) == '/') {
      $close1='/';
      $parms=substr($parms, 0, -1);
    } else
      $close1 = '';

    if (substr($search, 0,1) == '/') {
      $close2='/';
      $search=substr($search, 1);
    } else
      $close2 = '';

    $search = str_replace ('#', '<font color="black"><b><font color="black">#</font></b></font>',  $search);


    $source = substr($source, 0, $start) 
            . '<b>#open#' . $close2 . '<font color="blue">'.$search.$space.'</font><font color="red">' 
            . $parms
            . '</font>'
            . $close1
            . '#close#</b>' 
            . substr($source, $end+1);

    goto go;

  }

?>