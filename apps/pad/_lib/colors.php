<?php
    

  function padColorsHighLight ( $source ) {

    $source = padUnescape ( $source );
    $source = highlight_string ( $source, TRUE );
    $source = str_replace ( "\r\n", '<br>', $source );
    $source = str_replace ( "\n",   '<br>', $source );

    return $source;

  }


  function padColorsFile ( $file ) {

    if (substr($file, -4) == '.pad')
      return padColorsString    ( padFileGetContents ($file) ) ;
    else
      return padColorsHighLight ( padFileGetContents ($file) ) ;
  
  }  


  function padColorsAt ( &$source ) {

   while ( padBetween ( $source, '@', '@', $before, $between, $after ) ) {

    if ( ! padValidTag ($between) ) {
      $source = "$before&at;$between&at;$after";
      continue;
    }

    $parts = padExplode ($between, ':', 2); 

    if ( count ( $parts ) == 2 ) 
      $between = '<font color="red">' 
                . $parts[0]
                . '</font><b><font color="black">:</font></b>' 
                . $parts[1];

     $source = $before . '<b><font color="black">&at;</font><font color="purple">' . $between . '</font><font color="black">&at;</font></b>' . $after;

   }

  }

  function padColorsString ( $source ) { 

    $source = str_replace ( '<!-- PAD: ABOVE -->',    '', $source );
    $source = str_replace ( '<!-- PAD: VERTICAL -->', '', $source );
    $source = str_replace ( '<!-- PAD: NO REGRESSION -->', '', $source );
    $source = str_replace ( '<!-- PAD: SKIP REGRESSION -->', '', $source );

    $source = padColorsHighLight ( trim ( $source ) );

go: $end = strpos($source, '}');

    if ( $end === FALSE ) {
      padColorsAt ( $source );
      return $source;
    }

    $start = strrpos ($source, '{', $end - strlen($source));

    if ( $start === FALSE ) {
      $source = substr($source, 0, $end) . '&close;' . substr($source, $end+1);
      goto go;
    }

    if ( ctype_space ( $source [$start+1] ) ) {
      $source = substr($source, 0, $start) . '&open;' . substr($source, $start+1);      
      goto go;      
    }

    $between = substr($source, $start+1, $end-$start-1);
    $char    = substr($between, 0, 1);
    $words   = preg_split ("/[\s]+/", $between, 2, PREG_SPLIT_NO_EMPTY);
    $tag     = trim($words[0] ?? '');
    $search  = $tag;
 
    if ( in_array ( $char, ['$','!','#','&','?','@'] ) ) {
      padColorsField ( $source, $start, $end, $between );
      goto go;
    }

    if ( $char == '/' ) {
      padColorsTag ( $source, $start, $end, $between, $words, $search );
      goto go;
    }     

    $ns_pos = strpos($tag, ':');
    if ($ns_pos) {
      $type = substr ($tag, 0, $ns_pos);
      $tag  = substr ($tag, $ns_pos+1);
    } 

    if ( ! padValidTag ( $tag ) )  {        
      $source = substr($source, 0, $start) . '&open;' . substr($source, $start+1);      
      goto go;       
    } 
  
    padColorsTag ( $source, $start, $end, $between, $words, $search );

    goto go;

  }


  function padColorsFieldOptions ( &$source, $start, $end, $between, $options ) { 

    $parts = padExplode ( $options [0], ':', 2);

    if ( count ( $parts ) == 2 )
      $field = '<font color="purple">' . $parts[0] . '</font>'
             . '<font color="black">:</font>'
             . '<font color="green">' . $parts[1] . '</font>';
    else
      $field = '<font color="green">' . $parts[0] . '</font>';

    $rest = '';

    for ($i=1; $i < count($options); $i++) {

      $parts = padExplode ( $options [$i], ':', 2);

      $rest .= ' <font color="black">|</font> '; 
      
      if ( count ( $parts ) == 2 )
        $rest .= '<font color="purple">' . $parts[0] . '</font>'
               . '<font color="black">:</font>'
               . '<font color="blue">' . $parts[1] . '</font>';
      else
        $rest .= '<font color="blue">' . $parts[0] . '</font>';

    }

    $source = substr($source, 0, $start) 
        . '<b>&open;' 
        . $field
        . $rest
        . '&close;</b>' 
        . substr($source, $end+1);  

    $source = str_replace ('@', '<font color="black"><b>@</b></font>', $source);

  }

  function padColorsField ( &$source, $start, $end, $between ) { 

    $options = padExplode($between, '|' );

    if ( count ( $options ) > 1  )
      return padColorsFieldOptions ( $source, $start, $end, $between, $options );

    $between = str_replace ('!', '<font color="red">!</font>', $between);
    $between = str_replace ('@', '<font color="red">@</font>', $between);
    $between = str_replace ('$', '<font color="red">$</font>', $between);
    $between = str_replace ('!', '<font color="red">!</font>', $between);
    $between = str_replace ('#', '<font color="red">#</font>', $between);
    $between = str_replace ('&amp;', '<font color="red">&</font>', $between);
    $between = str_replace (':', '<font color="black">:</font>', $between);

    $parts = padExplode ($between, '@', 2);

    if ( count ( $parts ) == 2 ) {


       $source = substr($source, 0, $start) 
                . '<b>&open;<font color="green">' 
                . $parts[0]
                . '</font><b><font color="red">@</font></b><font color="purple">' 
                . $parts[1]
                . '</font>&close;</b>' 
                . substr($source, $end+1);      

    } else { 
 
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

  }   


  function padColorsTag ( &$source, $start, $end, $between, $words, $search ) { 

    $parms  = $words[1] ?? '';

    $space  = ($parms) ? ' ' : '';

    $parms  = str_replace (":", '<font color="black">:</font>',  $parms);
    $parms  = str_replace ("'", '<font color="black">\'</font>',  $parms);
    $parms  = str_replace ('|', '<font color="black">|</font>',  $parms);
    $parms  = str_replace ('@', '<font color="black">@</font>',  $parms);
    $parms  = str_replace ('$', '<font color="red">$</font>',  $parms);
    $parms  = str_replace ('%', '<font color="black">%</font>',  $parms);
    $parms  = str_replace (',', '<font color="black">,</font>',  $parms);

    $parms  = str_replace ('&open;',  '<font color="black">&open;</font>',  $parms);
    $parms  = str_replace ('&close;', '<font color="black">&close;</font>', $parms);

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

  }


?>