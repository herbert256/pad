<?php


  function padCheckPage ( $app, $page ) {

    if ( ! preg_match ( '/^[A-Za-z0-9]+$/', $app  ) )    return FALSE;
    if ( trim($app) == '' )                              return FALSE;

    if ( ! is_dir (APPS . $app) )
      return FALSE;

    if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) ) return FALSE;
    if ( trim($page) == '' )                             return FALSE;

    if ( strpos($page, '//') !== FALSE)                  return FALSE;
    if ( substr($page, 0, 1) == '/')                     return FALSE;
    if ( substr($page, -1) == '/')                       return FALSE;

    $location = APPS . "$app/pages";
    $padart     = padExplode ($page, '/');
    
    foreach ($padart as $key => $value) {
      
      if ($value == 'inits') return FALSE;
      if ($value == 'exits') return FALSE;

      if ( $key == array_key_last($padart)
            and (file_exists("$location/$value.php") or file_exists("$location/$value.html") ) )
        return TRUE; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
      else
        return FALSE;
      
    }
    
    return ( file_exists("$location/index.php") or file_exists("$location/index.html") );
    
  }


  function padGetPage ( $app, $page ) {

    $location = APPS . "$app/pages";
    $padart     = padExplode ($page, '/');
    
    foreach ($padart as $key => $value)
      if ( $key == array_key_last($padart)
            and (file_exists("$location/$value.php") or file_exists("$location/$value.html") ) )
        return $page; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
   
    return "$page/index";

  }


?>