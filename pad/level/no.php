<?php

  // Fallback for a tag whose name resolved to no known type. With the optional or noError
  // option the tag is dropped silently; otherwise it is put back into the output verbatim,
  // escaped as &open;..&close; so the loop will not pick it up again - just the tag when it
  // is single, the whole open..close span when a closing tag was found. noError used to be
  // parsed and ignored, so the tag it was written on leaked its own source into the page.

  if ( in_array ( 'optional', $padPrmParse ) or in_array ( 'noError', $padPrmParse ) )
    if ( padValidTag ($padWords [0]) ) {

      // The silent drop is the option's whole working, and the tag never becomes a level -
      // so this is the one place the name can get on the xref record.

      global $padInfoXref;

      if ( $padInfoXref ?? FALSE )
        foreach ( [ 'optional', 'noError' ] as $padNoName )
          if ( in_array ( $padNoName, $padPrmParse ) )
            padInfoXref ( 'options', 'general', $padNoName );

      return include PAD . 'options/optional.php';

    }

  // Strict mode reports the name instead of keeping it - and a property written over a
  // level that is not there gets told which half is wrong.

  global $padCheckSyntax;

  // Every report here is a call, not a return: under an action that continues - ignore,
  // log - the walk falls through to the literal keeping below, which is the lenient
  // answer. A miss may then meet the generic catch-all too and speak twice in the log;
  // under every ending action the first report is the last.

  if ( $padCheckSyntax ) {

    $padNoTag = $padWords [0];

    // The two branch tags reach here only when they stand outside the pair that reads
    // them - if.php and case.php consume their own.

    // A brace pair starting # meant to be a comment and did not end with # - the walk
    // would otherwise call it an unknown tag named after the note itself.

    if ( str_starts_with ( $padNoTag, '#' ) )
      padError ( "the comment {# ... does not close with #}" );

    if ( $padNoTag == 'elseif' )
      padError ( "an {elseif} stands inside its {if}, before any @else@" );

    if ( $padNoTag == 'when' )
      padError ( "a {when} stands inside its {case}" );

    // A type prefix does not search, it asserts - so a miss is not an unknown tag, it is
    // a named thing of a known kind that is not there. The prefix says which kind.

    if ( str_contains ( $padNoTag, ':' ) ) {

      list ( $padNoPrefix, $padNoName ) = padSplitOnUnquotedColon ( $padNoTag );

      $padNoKinds = [
        'data'     => 'data store',        'bool'     => 'bool store',
        'content'  => 'content block',     'include'  => 'include',
        'local'    => 'file in _data',     'constant' => 'constant',
        'app'      => 'application tag',   'common'   => 'tag in _common',
        'pad'      => 'built-in tag',      'php'      => 'PHP function',
        'script'   => 'script',            'select'   => 'declared select table',
        'array'    => 'array',             'pull'     => 'stored sequence',
        'sequence' => 'sequence type',     'action'   => 'sequence action',
        'field'    => 'field',             'parm'     => 'parameter',
        'property' => 'property',          'level'    => 'level array',
        'flag'     => 'sequence type',     'keep'     => 'sequence type',
        'make'     => 'sequence type',     'remove'   => 'sequence type',
        'function' => 'function',
      ];

      if ( isset ( $padNoKinds [$padNoPrefix] ) ) {

        if ( str_contains ( $padNoName, '(' ) )
          $padNoName = strstr ( $padNoName, '(', TRUE );

        padError ( "there is no " . $padNoKinds [$padNoPrefix] . " named '" . trim ( $padNoName ) . "'" );

      }

      // The sequence forms read either way round - make:fibonacci and fibonacci:make -
      // so a known sequence word behind the colon says the front half is the miss.

      if ( in_array ( $padNoName, [ 'make', 'keep', 'flag', 'remove' ] ) )
        padError ( "there is no sequence type named '" . $padNoPrefix . "'" );

      // A colon with a name-shaped word in front of it is a type prefix - just not one
      // the engine has.

      if ( padValidVar ( $padNoPrefix ) )
        padError ( "there is no type named '" . $padNoPrefix . "'" );

    }

    if ( str_contains ( $padNoTag, '@' ) ) {

      $padNoProp   = strstr ( $padNoTag, '@', TRUE );
      $padNoTarget = substr ( strstr ( $padNoTag, '@' ), 1 );

      if ( file_exists ( PAD . "properties/$padNoProp.php" ) )
        padError ( "there is no enclosing level named '$padNoTarget' for the property '$padNoProp'" );

      // The mirror: the level is there, the property is not.

      for ( $padNoKey = $pad; $padNoKey >= 0; $padNoKey-- )
        if ( ( $padName [$padNoKey] ?? '' ) == $padNoTarget )
          padError ( "there is no property named '$padNoProp'" );

    }

    padError ( "there is no tag named '$padNoTag'" );

  }

  if ( $padPairSet ) return padLevelNoPair   ();
  else               return padLevelNoSingle ();

?>