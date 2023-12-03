<?php


  function refLink () {

    global $padPage;

    if ( str_starts_with ($padPage, 'reference' ) ) return TRUE;
    if ( $padPage == 'index'                      ) return FALSE;

    $types = padData ('references.json');

    foreach ( $types as $key => $value ) {
      if ( str_starts_with ( $padPage, $value ['ref'] . '/' ) )
        return TRUE;

    }

    return FALSE;

  }
  
  function forLink () {

    return 'reference/xref'
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

    if ( ! $GLOBALS ['go'] )
      return '';

    return forLink () . '&second='  . $GLOBALS ['second'] ;

  }  

  function parts ( ) {

    global $padPage, $manual, $parts; 

    $parts = [];

    if ( $padPage == 'index' ) {
      $parts ['home'] ['part'] = 'home';
      $parts ['home'] ['link'] = '';    
    } else {
      $parts ['home'] ['part'] = 'home';
      $parts ['home'] ['link'] = 'index';             
    }   

    if ( $padPage == 'index' ) {
      $parts ['man'] ['part'] = 'manual';
      $parts ['man'] ['link'] = 'manual';  
      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'reference';  
      $parts ['dev'] ['part'] = 'develop';
      $parts ['dev'] ['link'] = 'develop';
      return $parts;
    }

    if ( $padPage == 'reference/xref' ) {

      $parts ['d'] ['part'] = 'reference';
      $parts ['d'] ['link'] = 'reference';

      $parts ['f'] ['part'] = strtolower ( $GLOBALS ['for'] );
      $parts ['f'] ['link'] = '';

      $parts ['i'] ['part'] = $GLOBALS ['xitem'];
      $parts ['i'] ['link'] = itemLink ();

      if ( $GLOBALS ['second'] ) {
        $parts ['s'] ['part'] = $GLOBALS ['second'];
        $parts ['s'] ['link'] = secondLink ();
      }

      if ( $GLOBALS ['go'] ) {
        $parts ['g'] ['part'] = $GLOBALS ['go'];
        $parts ['g'] ['link'] = '';
      
      } 

      return $parts;
    
    }

    if ( $padPage == 'develop/xref' ) {

      $parts ['dev'] ['part'] = 'develop';
      $parts ['dev'] ['link'] = 'develop';

      if ( $GLOBALS ['xref'] or $GLOBALS ['go'] ) {
        $parts ['x'] ['part'] = 'cross reference';
        $parts ['x'] ['link'] = 'develop/xref';
      } else {
        $parts ['x'] ['part'] = 'cross reference';
        $parts ['x'] ['link'] = '';
      }     

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

        $parts ['lst'] ['part'] = $last;

        if ( $GLOBALS ['go'] ) 
          $parts ['lst'] ['link'] = "develop/xref&xref=$xref/$last";
        else   
          $parts ['lst'] ['link'] = '';     

      }

      if ( $GLOBALS ['go'] ) {
        $parts ['go'] ['part'] = substr ( $GLOBALS ['go'], 1 );
        $parts ['go'] ['link'] = '';     
      } 

      return $parts;
    
    }

    $refLink = refLink();

    if ( $padPage == 'manual/index') {

      if ( ! $manual ) {

        $parts ['man'] ['part'] = 'manual';
        $parts ['man'] ['link'] = '';  

        return $parts;

      }

      $parts ['man'] ['part'] = 'manual';
      $parts ['man'] ['link'] = 'manual';  

      $parts ['now'] ['part'] = $manual;
      $parts ['now'] ['link'] = '';  

      return $parts;

    } elseif ( $padPage == 'reference/index') {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = '';    

      return $parts;

    } elseif ( $refLink ) {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'reference'; 

    }  

    if     ( strpos ($padPage, '/show/' )      ) $source = 'develop/regression/show';
    elseif ( $padPage == 'reference/index' ) $source = $GLOBALS['reference'];
    elseif ( $padPage == 'reference/show'      ) $source = $GLOBALS['item'];
    else                                         $source = $padPage;

    $source = str_replace ( '/index', '', $source ); 
    $source = str_replace ( '/docs', '', $source ); 
    $source = padExplode ( $source, '/' );

    $link = '';

    foreach ( $source as $key => $part ) {
          
      $link = ($link) ? "$link/$part" : $part;

      $parts [$key] ['part'] = $part;
 
      if ( $refLink and $key <> array_key_last ($source)) {

        $parts [$key] ['link'] = "reference/index`&reference=$link";

      } elseif ( $key == array_key_last ($source) ) {

        $parts [$key] ['link'] = '';

      } else {

        $parts [$key] ['link'] =  $link;

        if ( $part == 'regression')
          $parts [$key] ['link'] .= '&fromMenu=1';

      }

    }

    return $parts;

  }


?>