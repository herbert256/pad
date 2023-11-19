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
    . '&xref='  . ($GLOBALS ['xref'] ?? '')
    . '&for='   . urlencode(($GLOBALS ['for'] ?? ''))
    . '&xmain=' . ($GLOBALS ['xmain'] ?? '')
    . '&xitem=' . ($GLOBALS ['xitem'] ?? '');

  }

  function parts ( ) {

    global $padPage, $manual; 

    if ( $padPage == 'index' ) {
      $parts ['home'] ['part'] = 'home';
      $parts ['home'] ['link'] = '';    
    } else {
      $parts ['home'] ['part'] = 'home';
      $parts ['home'] ['link'] = 'index';             
    }   

    if ( $padPage == 'index' ) {
      $parts ['man']  ['part'] = 'manual';
      $parts ['man']  ['link'] = 'manual';  
      $parts ['ref']  ['part'] = 'reference';
      $parts ['ref']  ['link'] = 'reference';  
      $parts ['dev']  ['part'] = 'develop';
      $parts ['dev']  ['link'] = 'develop';
      return $parts;
    }

    if ( $padPage == 'reference/xref' ) {

      $parts ['dev']  ['part'] = 'reference';
      $parts ['dev']  ['link'] = 'reference';

      if ( $GLOBALS ['xgo'] ) {

        if ( $GLOBALS ['xmain'] ) {
          $parts ['xm']  ['part'] = strtolower ( $GLOBALS ['for'] );
          $parts ['xm']  ['link'] = forLink ();
        }

        if ( $GLOBALS ['xitem'] ) {
          $parts ['xi']  ['part'] = $GLOBALS ['xitem'];
          $parts ['xi']  ['link'] = $GLOBALS ['xitem'];
        }

        if ( $GLOBALS ['xnext'] ) {
          $parts ['xn']  ['part'] = $GLOBALS ['xnext'];
          $parts ['xn']  ['link'] = $GLOBALS ['xnext'];
        }

        $parts ['x']  ['part'] = $GLOBALS ['xgo'];
        $parts ['x']  ['link'] = '';
      
      } elseif ( $GLOBALS ['xnext'] ) {

        if ( $GLOBALS ['xmain'] ) {
          $parts ['xm']  ['part'] = strtolower ( $GLOBALS ['for'] );
          $parts ['xm']  ['link'] = forLink ();
        }

        if ( $GLOBALS ['xitem'] ) {
          $parts ['xi']  ['part'] = $GLOBALS ['xitem'];
          $parts ['xi']  ['link'] = $GLOBALS ['xitem'];
        }

        $parts ['xn']  ['part'] = $GLOBALS ['xnext'];
        $parts ['xn']  ['link'] = '';

      } elseif ( $GLOBALS ['xitem'] ) {

        if ( $GLOBALS ['xmain'] ) {
          $parts ['xm']  ['part'] = strtolower ( $GLOBALS ['for'] );
          $parts ['xm']  ['link'] = forLink ();
        }

        $parts ['xi']  ['part'] = $GLOBALS ['xitem'];
        $parts ['xi']  ['link'] = '';
      
      } elseif ( $GLOBALS ['xmain'] ) {

        $parts ['xm']  ['part'] = strtolower ( $GLOBALS ['for'] );
        $parts ['xm']  ['link'] = '';
        
      }

      return $parts;
    
    }

    if ( $padPage == 'develop/xref' ) {

      $parts ['dev']  ['part'] = 'develop';
      $parts ['dev']  ['link'] = 'develop';

      if ( $GLOBALS ['xref'] or $GLOBALS ['go'] ) {
        $parts ['x']  ['part'] = 'cross reference';
        $parts ['x']  ['link'] = 'develop/xref';
      } else {
        $parts ['x']  ['part'] = 'cross reference';
        $parts ['x']  ['link'] = '';
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

        $parts ['lst']  ['part'] = $last;

        if ( $GLOBALS ['go'] ) 
          $parts ['lst']  ['link'] = "develop/xref&xref=$xref/$last";
        else   
          $parts ['lst']  ['link'] = '';     

      }

      if ( $GLOBALS ['go'] ) {
        $parts ['go']  ['part'] = substr ( $GLOBALS ['go'], 1 );
        $parts ['go']  ['link'] = '';     
      } 

      return $parts;
    
    }

    $refLink = refLink();

    if ( $padPage == 'manual/index') {

      if ( ! $manual ) {

        $parts ['man']  ['part'] = 'manual';
        $parts ['man']  ['link'] = '';  

        return $parts;

      }

      $parts ['man']  ['part'] = 'manual';
      $parts ['man']  ['link'] = 'manual';  

      $parts ['now']  ['part'] = $manual;
      $parts ['now']  ['link'] = '';  

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
    elseif ( $padPage == 'reference/reference' ) $source = $GLOBALS['reference'];
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

        $parts [$key] ['link'] = "reference/reference&reference=$link";

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