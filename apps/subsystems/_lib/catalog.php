<?php

  include_once COMMON . '_lib/subsystemCatalog.php';
  include_once COMMON . '_lib/referenceCatalog.php';

  // Every registered subsystem, once: a tag namespace takes precedence when the same
  // family also supplies kinds to a helper. Membership always comes from the engine.

  function subsystemsCatalog () {

    static $catalog = NULL;
    if ( $catalog !== NULL ) return $catalog;

    $catalog = [];
    $info = subsystemsInfo ();

    foreach ( padSubsystems as $name => [ $directory ] ) {
      $comment = appReferenceHandlerComment ( $name );
      [ $about, $group, $syntax ] = $info [$name] ?? [ appReferenceFirstSentence ( $comment ), 'subsystems', "{{$name}:name/}" ];
      $label = [ 'ai' => 'AI', 'jsonld' => 'JSON-LD', 'codes' => 'Barcodes' ] [$directory] ?? ucfirst ( $directory );
      $catalog [$name] = [ 'name' => $name, 'key' => $label, 'link' => "subsystem/$name", 'directory' => $directory,
                          'about' => $about, 'group' => $group, 'syntax' => $syntax, 'comment' => $comment,
                          'prefix' => TRUE, 'language' => 'pad' ];
    }

    foreach ( appSubsystemFamilies () as $name => $family ) {
      $sample = appSubsystemExample ( $name, $family ['example'] ?: ( padKinds ( $family ['directory'] ) [0] ?? '' ) );
      $catalog [$name] = [ 'name' => $name, 'key' => $family ['label'], 'link' => "subsystem/$name", 'directory' => $family ['directory'],
                          'about' => $family ['about'], 'group' => 'kinds', 'syntax' => $sample ['source'],
                          'comment' => '', 'prefix' => FALSE, 'language' => $sample ['language'] ];
    }

    uasort ( $catalog, fn ( $a, $b ) => strcmp ( $a ['group'], $b ['group'] ) ?: strcasecmp ( $a ['key'], $b ['key'] ) );
    return $catalog;

  }

  function subsystemsMembers ( $name ) {

    global $padRoot;

    if ( isset ( padSubsystems [$name] ) )
      return array_map ( fn ( $kind ) => [ "$name:$kind", "{$padRoot}tags/?tag/$name/$kind" ], padSubsystemTagKinds ( $name ) );

    if ( isset ( padSubsystemKinds [$name] ) )
      return array_map ( fn ( $kind ) => [ $kind, "{$padRoot}tags/?kind/$name/$kind" ], padKinds ( padSubsystemKinds [$name] ) );

    return [];

  }

  function subsystemsExamples ( $name ) {

    $examples = appReferenceExamples ( $name, 'subsystems' );
    if ( $examples ) return $examples;

    if ( isset ( appSubsystemFamilies () [$name] ) ) {
      $family = appSubsystemFamilies () [$name];
      $sample = appSubsystemExample ( $name, $family ['example'] ?: ( padKinds ( $family ['directory'] ) [0] ?? '' ) );
    } else {
      $info = subsystemsCatalog () [$name];
      $sample = [ 'source' => $info ['syntax'], 'language' => 'pad', 'run' => FALSE,
                  'about' => 'Supply the values shown in the syntax when using this subsystem in your page.' ];
    }

    return [ $sample + [ 'page' => '', 'title' => subsystemsCatalog () [$name] ['key'], 'file' => '', 'php' => '', 'phpHtml' => '',
                        'padHtml' => padHighlightTokens ( $sample ['source'], $sample ['language'] ) ] ];

  }

  function subsystemsRow ( $subsystem ) {

    return $subsystem + [ 'groupLabel' => subsystemsGroups () [$subsystem ['group']] [0],
                          'members' => count ( subsystemsMembers ( $subsystem ['name'] ) ),
                          'examples' => count ( subsystemsExamples ( $subsystem ['name'] ) ) ];

  }

  function subsystemsByGroup () {

    $rows = [];
    foreach ( subsystemsGroups () as $key => [ $label, $tagline ] ) {
      $subsystems = [];
      foreach ( subsystemsCatalog () as $subsystem )
        if ( $subsystem ['group'] === $key ) $subsystems [] = subsystemsRow ( $subsystem );
      if ( $subsystems ) $rows [] = compact ( 'key', 'label', 'tagline', 'subsystems' ) + [ 'count' => count ( $subsystems ) ];
    }
    return $rows;

  }

  function subsystemsByName () {

    $rows = array_values ( array_map ( 'subsystemsRow', subsystemsCatalog () ) );
    usort ( $rows, fn ( $a, $b ) => strcasecmp ( $a ['key'], $b ['key'] ) );
    return $rows;

  }

  function subsystemsCheatSheet () {

    $rows = subsystemsByGroup ();
    foreach ( $rows as &$group )
      foreach ( $group ['subsystems'] as &$subsystem )
        $subsystem ['syntaxHtml'] = padHighlightTokens ( $subsystem ['syntax'], $subsystem ['language'] );
    return $rows;

  }

?>
