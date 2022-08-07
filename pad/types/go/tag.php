<?php

  $pTag_go = $pTag_base."tags/$pTag";

  $pTag_php = '';

  if ( file_exists("$pTag_go.html") )
  	$pTag_content .= pGet_html ("$pTag_go.html");

  if ( file_exists("$pTag_go.php") ) {

    ob_start();

    if ( $pTag_base == APP )
      pTiming_start ('app');

    $pTag_php = include "$pTag_go.php";

    if ( $pTag_base == APP )
      pTiming_end ('app');

    if ( $pTag_php === 1 )
      $pTag_php = '' ;

    $pTag_content .= ob_get_clean() ;

  }

  if ( is_array($pTag_php) or is_object($pTag_php) or is_resource($pTag_php) )
    return $pTag_php;

  if ( $pTag_php !== TRUE and $pTag_php !== FALSE and $pTag_php !== NULL ) {
    if ( $pTag_php or $pTag_content )
      $pTag_php = $pTag_content . $pTag_php;
    $pTag_content = '';
  }

  return $pTag_php;

?>