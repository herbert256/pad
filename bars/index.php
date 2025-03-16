<?php

  $ace = ace ( "C:\git\intbuild_ace-mutatie-scripts\build" );

  if ( ! count ( $ace ) )
    pad_error ( "No bar files found");

  $env = [ 'dev', 'tst', 'acc', 'prd' ];

  foreach ( $ace as $is => $bars )
    foreach ( $bars as $bar => $versions )
      if ( $bar <> 'deprecatedsharedclasses' )
        foreach ( $env as $env )
          $complete [$is] [$bar] [$env] = $all [$is] ['bars'] [$bar] [$env] = $versions [$env] ?? '';

  foreach ( $complete as $is => $bars )
    foreach ( $bars as $bar => $versions ) 
      foreach ( $env as $env )
        if ( str_contains ( $versions [$env], '-beta-') )
          $complete [$is] [$bar] [$env] = substr ( $versions [$env], 0, strpos ( $versions [$env], '-beta-' ) );
        
  foreach ( $complete as $is => $bars ) 
    foreach ( $bars as $bar => $versions ) 
      foreach ( $env as $env )
        if ( $versions ['dev'] <> $versions [$env] ) 
          $wip [$is] ['bars'] [$bar] = $versions ;
 
?>