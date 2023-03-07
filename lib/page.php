<?php


  function padCheckPage ( $app, $page ) {

    if ( ! preg_match ( '/^[a-zA-Z0-9]+$/',   $app  ) )           return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9_\/]*$/', $page ) )  return FALSE;
    if ( trim($app) == '' )                                       return FALSE;
    if ( trim($page) == '' )                                      return FALSE;
    if ( strpos($page, '//') !== FALSE)                           return FALSE;
    if ( substr($page, -1) == '/')                                return FALSE;
    if ( ! is_dir (APPS . $app) )                                 return FALSE;

    $location = APPS . "$app/pages";
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value) {
      
      if ($value == '_lib')   return FALSE;
      if ($value == '_inits') return FALSE;
      if ($value == '_exits') return FALSE;

      if ( $key == array_key_last($part)
            and (padExists("$location/$value.php") or padExists("$location/$value.html") ) )
        return TRUE; 

      if ( is_dir ("$location/$value") )
        $location.= "/$value";
      else
        return FALSE;
      
    }
    
    return ( padExists("$location/index.php") or padExists("$location/index.html") );
    
  }


  function padGetPage ( $app, $page ) {

    $location = APPS . "$app/pages";
    $part     = padExplode ($page, '/');
    
    foreach ($part as $key => $value)
      if ( $key == array_key_last($part)
            and (padExists("$location/$value.php") or padExists("$location/$value.html") ) )
        return $page; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
   
    return "$page/index";

  }


?>