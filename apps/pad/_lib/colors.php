<?php
    

  function padHighLight ($source) {

    $source = padUnescape ($source);

    return str_replace(
             '<code>', 
             '<code style="font-family:courier new,courier,monospace;font-size:12px;">', 
               highlight_string ($source, TRUE)
           );

  }


  function padColorsFile ($file) {

    if ( ! padExists($file) )
      return $file;

    if (substr($file, -4) == '.pad')
      return padColorsString ( trim(padFileGetContents($file)) ) ;
    else
      return padHighLight ( trim(padFileGetContents($file)) ) ;
  
  }  
  
  function padColorsKind ( $kind, &$source ) {

    $source = str_replace ( 
      "@$kind@", 
      '<b><font color="black">@</font><font color="purple">' . $kind . '</font><font color="black">@</font></b>', 
      $source); 

  } 

  function padColorsString ($source) { 

   $source = padHighLight (trim($source));

   padColorsKind ( 'content', $source ); 
   padColorsKind ( 'tidy',    $source ); 
   padColorsKind ( 'else',    $source ); 
   padColorsKind ( 'start',   $source ); 
   padColorsKind ( 'end',     $source ); 

go: $end = strpos($source, '}');

    if ( $end === FALSE ) {
      #$source = str_replace ('{', '&open;', $source);
      #$source = str_replace ('}', '&close;', $source);
      return $source;
    }

    $start = strrpos ($source, '{', $end - strlen($source));
    
    if ( $start === FALSE ) {
      $source = substr($source, 0, $end)
              . '&close;'
              . substr($source, $end+1);
      goto go;
    }

    $between = substr($source, $start+1, $end-$start-1);
    $char    = substr($between, 0, 1);
    $words   = preg_split ("/[\s]+/", $between, 2, PREG_SPLIT_NO_EMPTY);
 
    if ( $char == '$' or $char == '!'  or $char == '#'  or $char == '&' ) {

      $between = str_replace ('$', '<font color="red">$</font>', $between);
      $between = str_replace ('!', '<font color="red">!</font>', $between);
      $between = str_replace ('#', '<font color="red">#</font>', $between);
      $between = str_replace ('&amp;', '<font color="red">&</font>', $between);

      $parts = padExplode ($between, '@', 2);

      if ( count ( $parts ) == 2 )
  
         $source = substr($source, 0, $start) 
                  . '<b>&open;<font color="green">' 
                  . $parts[0]
                  . '</font><b><font color="black">@</font></b><font color="purple">' 
                  . $parts[1]
                  . '</font>&close;</b>' 
                  . substr($source, $end+1);      
  
      else { 

        $parts = padExplode ($between, ':', 3);

        if ( count ( $parts ) == 3 )
          $source = substr($source, 0, $start) 
                  . '<b>&open;<font color="purple">' 
                  . $parts[0]
                  . '</font><b><font color="black">:</font></b><font color="orange">' 
                  . $parts[1]
                  . '</font><b><font color="black">:</font></b><font color="green">' 
                  . $parts[2]
                  . '</font>&close;</b>' 
                  . substr($source, $end+1);      
        elseif ( count ( $parts ) == 2 )
          $source = substr($source, 0, $start) 
                  . '<b>&open;<font color="purple">' 
                  . $parts[0]
                  . '</font><b><font color="black">:</font></b><font color="green">' 
                  . $parts[1]
                  . '</font>&close;</b>' 
                  . substr($source, $end+1);      
        else
          $source = substr($source, 0, $start) 
                  . '<b>&open;<font color="green">' 
                  . $between 
                  . '</font>&close;</b>' 
                  . substr($source, $end+1);   

      }

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
      $type = substr ($tag, 0, $ns_pos);
      $tag  = substr ($tag, $ns_pos+1);
    } 

    if ( ! padValidTag ( $tag ) )  {             
      $source = substr($source, 0, $start) 
                . '&open;' . $between . '&close;' 
                . substr($source, $end+1);
      goto go;
    } 

co: $parms  = $words[1] ?? '';

    $space  = ($parms) ? ' ' : '';
    $parms  = str_replace ('<font color="green">', '',  $parms);
    $parms  = str_replace ('</font>', '',             $parms);
    $parms  = str_replace ('</b>', '',             $parms);
    $parms  = str_replace (' ', '&nbsp;',                        $parms);
    $parms  = str_replace ('=ToDo', '<font color="black">=</font>',  $parms);
    $parms  = str_replace ('|', '<font color="black">|</font>',  $parms);
    $parms  = str_replace ('@', '<font color="black">@</font>',  $parms);
    $parms  = str_replace ('$', '<font color="black">$</font>',  $parms);
    $parms  = str_replace ('%', '<font color="black">%</font>',  $parms);
    $parms  = str_replace (',', '<font color="black">,</font>',  $parms);
    $parms  = str_replace ('&open;', '<font color="black">&open;</font>',  $parms);
    $parms  = str_replace ('&close;', '<font color="black">&close;</font>', $parms);

    $x = padExplode($parms, '<font color="black">&open;</font><font color="black">$</font>', 2);
    if ( count($x) == 2 ) {
      $y = padExplode($x[1], '<font color="black">&close;</font>', 2);
      if ( count($y) == 2 ) 
        $parms = $x[0] . '<font color="black">&open;</font><font color="green">$'  . $y[0] . '</font><font color="black">&close;</font>' . $y[1];
    }

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

    $parts = padExplode ($search, ':');
    if (count ($parts) == 2 )
      $search = '<font color="purple">' . $parts[0] . '</font><font color="black"><b>:</b></font>' . $parts[1];
        
    $search = str_replace ('#', '<font color="black"><b><font color="black">#</font></b></font>',  $search);
    $search = str_replace ('@', '<font color="black"><b><font color="red">@</font></b></font>',  $search);
    $sparms = str_replace ('%', '<font color="black"><b><font color="black">%</font></b></font>',  $parms);

    $source = substr($source, 0, $start) 
            . '<b>&open;' . $close2 . '<font color="blue">'.$search.$space.'</font><font color="red">' 
            . $parms
            . '</font>'
            . $close1
            . '&close;</b>' 
            . substr($source, $end+1);

    goto go;

  }

?>