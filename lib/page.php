<?php


  function padCheckPage ( $padApp, $padPage ) {

    if ( ! preg_match ( '/^[a-zA-Z0-9]+$/',   $padApp  ) )           return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9_\/]*$/', $padPage ) )  return FALSE;
    if ( trim($padApp) == '' )                                       return FALSE;
    if ( trim($padPage) == '' )                                      return FALSE;
    if ( strpos($padPage, '//') !== FALSE)                           return FALSE;
    if ( substr($padPage, -1) == '/')                                return FALSE;
    if ( ! is_dir (padApps . $padApp) )                                 return FALSE;

    $location = padApps . "$padApp/pages";
    $part     = padExplode ($padPage, '/');
    
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


  function padGetPage ( $padApp, $padPage ) {

    $location = padApps . "$padApp/pages";
    $part     = padExplode ($padPage, '/');
    
    foreach ($part as $key => $value)
      if ( $key == array_key_last($part)
            and (padExists("$location/$value.php") or padExists("$location/$value.html") ) )
        return $padPage; 
      elseif ( is_dir ("$location/$value") )
        $location.= "/$value";
   
    return "$padPage/index";

  }


?>