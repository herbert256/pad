<?php

  // Resolves the tag name to a type, that is, which types/<type>.php will run it.
  //
  // Every named tag selects its namespace explicitly. Variable/expression sigils are
  // handled before this step. padTypeTagCheck() checks the selected type; padTypeSeq()
  // handles the sequence operation prefixes. A missing or unknown prefix leaves FALSE
  // for level/no.php: strict mode reports it, lenient mode preserves the literal text.

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;
  if ( str_ends_with ( $padTagCheck, ':' ) )
    $padTypeExplode [] = '';

  $padTypeSeq     = '';

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypePrefix = '';
    $padTypeCheck  = $padTagCheck;
    // Named tags always declare their namespace. Expression and variable sigils
    // have already been handled by level/level.php and do not pass through here.
    $padTypeResult = FALSE;

  } else {

    $padTypeGiven  = TRUE;

    $padTypePrefix = $padTypeExplode [0];
    $padTypeCheck  = $padTypeExplode [1];

    // A subsystem may select its kind from a field, without turning field output into
    // template source: {icon:$icon/}, {chart:$kind}...{/chart:$kind}. The namespace is
    // literal, the field supplies only a registered kind, and pair matching keeps the
    // spelling in the source. PHP and application tags cannot be selected this way.
    if ( ( isset ( padSubsystems [$padTypePrefix] ) or in_array ( $padTypePrefix, padSequenceOperations, TRUE ) )
         and preg_match ( '/^\$([A-Za-z][A-Za-z0-9_]*)$/D', $padTypeCheck, $padKindField ) ) {
      $padKindValue = padFieldValue ( $padKindField [1], 0 );
      $padTypeCheck = is_string ( $padKindValue ) ? trim ( $padKindValue ) : '';
      if ( isset ( padSubsystems [$padTypePrefix] ) )
        $padTypeCheck = strtolower ( $padTypeCheck );
    }

    $padTypeResult = padTypeTagCheck ( $padTypePrefix, $padTypeCheck );

    if ( ! $padTypeResult )
      $padTypeResult = padTypeSeq ( $padTypePrefix, $padTypeCheck );

    // A name is one prefix and one name. What stood behind a second colon was dropped, and
    // {field:name:zzz} answered the field name, {constant:PHP_INT_SIZE:junk} the constant -
    // a path written with colons read as its head. Such a name is no tag: level/no.php names
    // it under the strict check, and the lenient walk keeps it as text.

    if ( count ( $padTypeExplode ) > 2 )
      $padTypeResult = FALSE;

  }

?>
