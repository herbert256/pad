<?php

  // pad down <app> [--secret[=word]] [--retry=60] [--message='...']: takes an application
  // down for maintenance (pad/lib/maintenance.php) - every web request of it answers 503
  // with a Retry-After, its _errors/503.pad when it has one, until pad up. With --secret
  // the address ?<word> of the application gives the browser that asks it a cookie that
  // lets it through; --secret alone makes up a word and prints it. pad down without an
  // application lists the applications that are down.

  if ( ! defined ( 'DATA' ) )
    define ( 'DATA', cliHome () . '/DATA/' );

  include_once cliHome () . '/pad/lib/maintenance.php';

  $downArgs    = array_slice ( $argv, 2 );
  $downApp     = '';
  $downSecret  = '';
  $downRetry   = 60;
  $downMessage = '';

  foreach ( $downArgs as $downArg )
    if     ( $downArg === '--secret' )                                             $downSecret  = bin2hex ( random_bytes ( 8 ) );
    elseif ( preg_match ( '/^--secret=([A-Za-z0-9_-]{6,64})$/D', $downArg, $m ) )  $downSecret  = $m [1];
    elseif ( preg_match ( '/^--retry=([0-9]{1,7})$/D',            $downArg, $m ) )  $downRetry   = (int) $m [1];
    elseif ( preg_match ( '/^--message=(.*)$/Ds',                 $downArg, $m ) )  $downMessage = trim ( preg_replace ( '/\s+/', ' ', $m [1] ) );
    elseif ( $downApp === '' and ! str_starts_with ( $downArg, '-' ) )                $downApp     = $downArg;
    else
      return cliFail ( "pad down <app> [--secret[=word]] [--retry=seconds] [--message=text] - not understood: $downArg"
                       . ( str_starts_with ( $downArg, '--secret=' ) ? ' (a secret is 6 to 64 letters, digits, _ and -)' : '' ) );

  if ( $downApp === '' ) {

    $downList = padMaintenanceList ();

    if ( ! $downList )
      cliOut ( 'No application is down' );

    foreach ( $downList as $downName => $downOne )
      cliOut ( sprintf ( '%-24s down since %s%s%s', $downName, date ( 'Y-m-d H:i', $downOne ['time'] ),
                         $downOne ['secret']  !== '' ? ', with a secret'                   : '',
                         $downOne ['message'] !== '' ? ' - ' . $downOne ['message'] : '' ) );

    return 0;

  }

  if ( ! cliApp ( $downApp ) )
    return cliFail ( "there is no application named '$downApp' - pad down <app>" );

  if ( ! padMaintenanceDown ( $downApp, $downSecret, $downRetry, $downMessage ) )
    return cliFail ( "could not write DATA/maintenance/$downApp.json" );

  cliOut ( "$downApp is down for maintenance - pad up $downApp brings it back" );

  if ( $downSecret !== '' )
    cliOut ( "Let yourself through: ?$downSecret" );

  return 0;

?>
