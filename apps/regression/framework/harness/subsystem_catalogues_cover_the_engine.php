<?php

  // Compare both catalogues with files on disk, independently of padKinds. A new subsystem
  // or kind must be discoverable; a helper kind must never pretend to be a tag prefix.

  include_once COMMON . '_lib/subsystemCatalog.php';
  include_once APPS . 'tags/_lib/kinds.php';
  include_once APPS . 'types/_lib/types.php';
  include_once APPS . 'types/_lib/catalog.php';

  $catalogChecks = [ 'prefix kinds' => TRUE, 'helper kinds' => TRUE, 'family membership' => TRUE,
                     'real usage' => TRUE, 'source-only services' => TRUE, 'resolution order' => TRUE,
                     'dotted formula route' => TRUE, 'type documentation' => TRUE, 'macro syntax' => TRUE,
                     'chart examples' => TRUE ];
  $prefixCatalog = tagsKinds ();
  $helperCatalog = appSubsystemCatalog ();
  $typeCatalog = typesCatalog ();
  $helperKeys = [];

  foreach ( padSubsystems + padSubsystemKinds as $family => $definition ) {
    $prefix = is_array ( $definition );
    $directory = $prefix ? $definition [0] : $definition;
    $disk = array_map ( fn ( $file ) => pathinfo ( $file, PATHINFO_FILENAME ), glob ( PAD . "subs/$directory/types/*.*" ) );
    if ( $prefix and in_array ( $family, padSubsystemAuto, TRUE ) ) $disk [] = 'auto';
    sort ( $disk );
    $members = typesMembers ( $family );
    $expectedMembers = array_map ( fn ( $kind ) => $prefix ? "$family:$kind" : $kind, $disk );
    $catalogChecks ['family membership'] &= array_column ( $members, 0 ) === $expectedMembers;
    $catalogChecks ['family membership'] &= isset ( $typeCatalog [$family] ) && $typeCatalog [$family] ['prefix'] === $prefix;

    foreach ( $disk as $kind ) {
      $key = "$family:$kind";
      $row = ( $prefix ? $prefixCatalog : $helperCatalog ) [$key] ?? [];
      $catalogChecks [$prefix ? 'prefix kinds' : 'helper kinds'] &= ( $row ['link'] ?? '' ) === ( $prefix ? "tag/$family/$kind" : "kind/$family/$kind" );
      if ( $prefix ) continue;
      $helperKeys [] = $key;
      $catalogChecks ['real usage'] &= trim ( $row ['syntax'] ?? '' ) !== '' && trim ( $row ['text'] ?? '' ) !== '';
      $catalogChecks ['real usage'] &= ! str_starts_with ( $row ['syntax'] ?? '', "{{$key}" );
      if ( in_array ( $family, [ 'ai', 'channel', 'rule', 'format', 'filter' ] ) )
        $catalogChecks ['source-only services'] &= ( $row ['example'] ['run'] ?? TRUE ) === FALSE;
    }
  }

  $catalogChecks ['helper kinds'] &= count ( $helperKeys ) === count ( $helperCatalog );
  foreach ( array_merge ( ...typesByOrder () ) as $row )
    $catalogChecks ['resolution order'] &= ( isset ( padSubsystems [$row ['name']] ) or ! isset ( padSubsystemKinds [$row ['name']] ) );

  $route = padPageRoute ( 'kind/formula/stdev.p', APPS . 'tags/' );
  $catalogChecks ['dotted formula route'] = ( $route ['page'] ?? '' ) === 'kind/[family]/[kind]'
                                      && ( $route ['vars'] ?? [] ) === [ 'family' => 'formula', 'kind' => 'stdev.p' ];
  foreach ( glob ( PAD . 'types/*.php' ) as $file ) {
    $name = basename ( $file, '.php' );
    $catalogChecks ['type documentation'] &= isset ( $typeCatalog [$name] ) && typesDoc ( $name ) !== '';
  }

  $catalogChecks ['macro syntax'] = preg_replace ( '/\s+/', '', strip_tags ( padCode ( typesInfo () ['macro'] [2] ) ) ) === 'One';

  // Each chart detail page must choose a real example with its data. A source scanner
  // that still looked for the old {chart ...} spelling chose an undefined rows store.
  foreach ( glob ( PAD . 'subs/charts/types/*.php' ) as $file ) {
    $example = tagsKindExample ( 'chart', basename ( $file, '.php' ) );
    $catalogChecks ['chart examples'] &= $example ['app'] === 'charts'
                                    && is_file ( APPS . 'charts/' . $example ['page'] . '.pad' );
  }

  $catalogAnswer = implode ( '; ', array_map ( fn ( $name, $ok ) => "$name: " . ( $ok ? 'yes' : 'NO' ), array_keys ( $catalogChecks ), $catalogChecks ) );

?>
