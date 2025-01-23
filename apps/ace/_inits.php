<?php

  $all = $wip = $pods = $groups = [];

  $envs = [ 'dev', 'tst', 'acc', 'prd' ];

  $aceCount  = 0;
  $wmb7Count = 0;

  $files = glob ( "C:\git\intbuild_ace-mutatie-scripts\build\intace_is-*\overlays\*\patch-integrationserver.yaml" );

  foreach ( $files as $file ) {

    $parts = explode ( 'intace_is-', $file      );
    $parts = explode ( "\\",         $parts [1] );

    $is  = $parts [0];
    $env = $parts [2];
  
    $collections ['ACE'] [$is] = TRUE;;

    $bars = yaml_parse_file ( $file );
    $bars = explode ( ',' , $bars ['spec'] ['barURL'] );

    foreach ( $bars as $bar ) {

      $bar = str_replace ( '.bar', '', $bar );

      $split = explode ( '/' , $bar);
      $split = explode ( '-' , end ($split) );

      if ( $split [0] == 'abz' ) { // There is always an exception to the naming conventions ...
        array_shift ( $split);
        $split [0] = 'abz-' . $split [0];
      }

      $bar     = strtolower ( array_shift ( $split ) );
      $version = implode ( '-' , $split );

      if ( $version == '0.0.0' ) { // Old WMB-7 bars have 0.0.0 as version.
        $version = 'n/a';
        $collections ['WMP-7'] [$bar] = TRUE;
      } elseif ( $bar <> 'deprecatedsharedclasses' )  {
        $collections ['APP'] [$bar] = TRUE;
      }

      if ( $bar <> 'deprecatedsharedclasses' ) // Every ACE IS has 'deprecatedsharedclasses'
        $dump [$is] [$bar] [$env] = $version;   

    }

  }

  foreach ( $dump as $is => $bars )
    foreach ( $bars as $bar => $versions )
      foreach ( $envs as $env ) {
        $complete [$is] [$bar] [$env]             = $versions [$env] ?? '';
        $all      [$is] ['bars'] [$bar] ['value'] = $bar;
        $all      [$is] ['bars'] [$bar] [$env]    = $complete [$is] [$bar] [$env];
      }

  foreach ( $complete as $is => $bars )
    foreach ( $bars as $bar => $versions ) 
      foreach ( $envs as $env )
        if ( str_contains ( $versions [$env], '-beta-') )
          $complete [$is] [$bar] [$env] = substr ( $versions [$env], 0, strpos ( $versions [$env], '-beta-' ) );
        
  foreach ( $complete as $is => $bars ) {

    $parts = explode ( '-' , $is );

    if ( count ($parts) == 3 and $parts [2] == 'bpr' )
      $group = $parts [0] . '-' . $parts [2];
    else
      $group = $parts [0];

    foreach ( $bars as $bar => $versions ) {

      foreach ( $envs as $env )
        if ( $versions ['dev'] <> $versions [$env] ) {
          $wip [$is] ['bars'] [$bar]           = $versions ;
          $wip [$is] ['bars'] [$bar] ['value'] = $bar;
        }

      if ( $versions ['acc'] == 'n/a' ) {

        $wmb7Count++;
 
        if ( isset ( $pods [$is] ['bars'] ['wmb7'] ['value']) )
          $pods [$is] ['bars'] ['wmb7'] ['value']++;
        else 
          $pods [$is] ['bars'] ['wmb7'] ['value'] = 1; 
 
        if ( isset ( $groups [$group] ['bars'] ['wmb7'] ['value']) )
          $groups [$group] ['bars'] ['wmb7'] ['value']++;
        else 
          $groups [$group] ['bars'] ['wmb7'] ['value'] = 1; 

      } else {
 
        $aceCount++;

        $pods   [$is]    ['bars'] [$bar] ['value'] = $bar;   
        $groups [$group] ['bars'] [$bar] ['value'] = $bar;    

      }

    }

  }

  $isCount  = count ( $complete );
  $grpCount = count ( $groups );

?>