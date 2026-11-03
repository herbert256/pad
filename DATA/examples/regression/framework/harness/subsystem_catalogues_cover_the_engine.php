<?php

  // Compare both catalogues with files on disk, independently of padKinds. A new subsystem
  // or kind must be discoverable; a helper kind must never pretend to be a tag prefix.

  include_once COMMON . '_lib/subsystemCatalog.php';
  include_once APPS . 'tags/_lib/kinds.php';
  include_once APPS . 'types/_lib/types.php';
  include_once APPS . 'types/_lib/catalog.php';
  include_once APPS . 'subsystems/_lib/subsystems.php';
  include_once APPS . 'subsystems/_lib/catalog.php';

  $catalogChecks = [ 'prefix kinds' => TRUE, 'helper kinds' => TRUE, 'family membership' => TRUE,
                     'real usage' => TRUE, 'source-only services' => TRUE, 'resolution order' => TRUE,
                     'dotted formula route' => TRUE, 'type documentation' => TRUE, 'macro syntax' => TRUE,
                     'chart examples' => TRUE, 'catalog separation' => TRUE, 'subsystem examples' => TRUE ];
  $prefixCatalog = tagsKinds ();
  $helperCatalog = appSubsystemCatalog ();
  $typeCatalog = typesCatalog ();
  $subsystemCatalog = subsystemsCatalog ();
  $helperKeys = [];

  foreach ( padSubsystems + padSubsystemKinds as $family => $definition ) {
    $prefix = is_array ( $definition );
    $directory = $prefix ? $definition [0] : $definition;
    $disk = array_map ( fn ( $file ) => pathinfo ( $file, PATHINFO_FILENAME ), glob ( PAD . "subs/$directory/types/*.*" ) );
    if ( $prefix and in_array ( $family, padSubsystemAuto, TRUE ) ) $disk [] = 'auto';
    sort ( $disk );
    $members = subsystemsMembers ( $family );
    $expectedMembers = array_map ( fn ( $kind ) => $prefix ? "$family:$kind" : $kind, $disk );
    $catalogChecks ['family membership'] &= array_column ( $members, 0 ) === $expectedMembers;
    $catalogChecks ['family membership'] &= isset ( $subsystemCatalog [$family] ) && $subsystemCatalog [$family] ['prefix'] === $prefix;

    $catalogChecks ['catalog separation'] &= ! isset ( $typeCatalog [$family] );
    $examples = subsystemsExamples ( $family );
    $catalogChecks ['subsystem examples'] &= count ( $examples ) > 0;
    foreach ( $examples as $example ) {
      $catalogChecks ['subsystem examples'] &= trim ( $example ['padHtml'] ) !== '';
      if ( $example ['page'] )
        $catalogChecks ['subsystem examples'] &= is_file ( APPS . 'subsystems/' . $example ['file'] );
    }

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
    $catalogChecks ['type documentation'] &= isset ( ( $typeCatalog + $subsystemCatalog ) [$name] ) && typesDoc ( $name ) !== '';
    $catalogChecks ['catalog separation'] &= isset ( $typeCatalog [$name] ) !== isset ( $subsystemCatalog [$name] );
  }

  $catalogChecks ['catalog separation'] &= count ( $subsystemCatalog ) === count ( padSubsystems + padSubsystemKinds );

  $catalogChecks ['macro syntax'] = preg_replace ( '/\s+/', '', strip_tags ( padCode ( typesInfo () ['macro'] [2] ) ) ) === 'One';

  // Each chart detail page must choose a real example with its data. A source scanner
  // that still looked for the old {chart ...} spelling chose an undefined rows store.
  foreach ( glob ( PAD . 'subs/charts/types/*.php' ) as $file ) {
    $example = tagsKindExample ( 'chart', basename ( $file, '.php' ) );
    $app = $example ['app'] ?: 'tags';
    $exampleFile = APPS . $app . '/' . $example ['page'] . '.pad';
    $kind = basename ( $file, '.php' );
    $catalogChecks ['chart examples'] &= in_array ( $app, [ 'charts', 'tags' ], TRUE )
                                    && $example ['run'] && is_file ( $exampleFile )
                                    && preg_match ( '/\{chart:' . preg_quote ( $kind, '/' ) . '\\b/', (string) file_get_contents ( $exampleFile ) );
  }

  $catalogAnswer = implode ( '; ', array_map ( fn ( $name, $ok ) => "$name: " . ( $ok ? 'yes' : 'NO' ), array_keys ( $catalogChecks ), $catalogChecks ) );

?>
