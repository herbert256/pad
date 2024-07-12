<?php
  

  function parts ( ) {

    global $padPage, $manual, $parts, $item; 

    $parts = [];
    return $parts;  

    if ( ! str_starts_with ( $padPage, 'index') and 
         ! str_starts_with ( $padPage, 'manual/') and 
         ! str_starts_with ( $padPage, 'xapp/') and 
         ! str_starts_with ( $padPage, 'develop/') ) {

      $parts ['dev'] ['part'] = $padPage;
      $parts ['dev'] ['link'] = '';

      return $parts;

    } 

    if ( $padPage == 'manual/index' and $manual ) {
      
      $parts ['now'] ['part'] = $manual;
      $parts ['now'] ['link'] = '';  

    } 

    if ( str_starts_with ( $padPage, 'develop/') and $padPage <> 'develop/index') {
 
      if ( str_starts_with ( $padPage, 'develop/show') ) {
        $parts ['now'] ['part'] = $item;
        $parts ['now'] ['link'] = '';
      } else {
        $parts ['dev'] ['part'] = $padPage;
        $parts ['dev'] ['link'] = '';      
      }

    }

    if ( $padPage == 'xapp/xref' or $padPage == 'xapp/go' ) {

      $parts ['f'] ['part'] = strtolower ( $GLOBALS ['for'] );
      $parts ['f'] ['link'] = '';

      $parts ['i'] ['part'] = $GLOBALS ['xitem'];
      $parts ['i'] ['link'] = '';

 #     if ( $GLOBALS ['second'] ) {
 #       $parts ['s'] ['part'] = $GLOBALS ['second'];
 #       $parts ['s'] ['link'] = secondLink ();
 #     }
    
    }

    if ( $padPage == 'develop/xref' ) {

      if ( $GLOBALS ['xref'] ) {

        $plode = padExplode ( $GLOBALS ['xref'], '/' );
        $key   = array_key_last ($plode);
        $last  = $plode [$key];

        unset ( $plode [$key] );

        $xref = '';

        foreach ( $plode as $key => $value ) {
          $xref .= "/$value";
          $parts ["x$key"] ['part'] = $value;
          $parts ["x$key"] ['link'] = "develop/xref&xref=$xref";
        }

      }
    
    }

    return $parts;

  }



  function forLink () {

    return 'xapp/xref'
    . '&first='  . ($GLOBALS ['first'] )
    . '&for='    . urlencode(($GLOBALS ['for'] ))
    . '&xitem='  . ($GLOBALS ['xitem'] );

  }  


  function itemLink () {

    if ( ! $GLOBALS ['second'] )
      return '';

    return forLink ();

  }


  function secondLink () {

    if ( ! isset ($GLOBALS ['go'] ) )
      return '';

    return forLink () . '&second='  . $GLOBALS ['second'] ;

  }  
  
  
?>