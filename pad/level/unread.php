<?php

  // Strict-mode sweep as the level closes: an option the template wrote and nothing ever
  // read is a silent no-op, which is usually a typo. Whatever legitimately reads an option
  // marks it done (padTagParm, the option walks, the sequence parameter intake), a
  // handling name is the handling walk's to read, and an application _options handler
  // counts as a reader.
  //
  // Only the built-in types and the sequence tags are swept: an application or _common
  // tag reads its options from its own template, and a branch not taken is allowed to
  // leave one unread - the {example} tag's onlyResult form reads none of its panel
  // switches.

  if ( $padTagSeq [$pad] or in_array ( $padType [$pad], [ 'pad', 'select' ] ) )

    foreach ( $padParms [$pad] as $padUnread ) {

      if ( $padUnread ['padPrmKind'] != 'option' )
        continue;

      $padUnreadName = $padUnread ['padPrmName'];

      if ( padIsDone ( $padUnreadName ) )
        continue;

      if ( file_exists ( PAD . "options/$padUnreadName.php" ) )
        continue;

      if ( file_exists ( PAD . "handling/types/$padUnreadName.php" ) )
        continue;

      // A piped tag can carry its pipe functions into the parsed options - {echo $d |
      // date('Y-m-d')} does - and a pipe function is not an unread option.

      if ( file_exists ( PAD . "functions/$padUnreadName.php" ) )
        continue;

      if ( isset ( $padOptionsAppStartCall [$pad] [$padUnreadName] ) )
        continue;

      // A sequence tag's words may also be its actions, its types, its stores, the
      // min/max option pair, or the subsystem's own mode words - readers of their own.

      if ( $padTagSeq [$pad] ) {
        if ( file_exists ( PA . "$padUnreadName.php" )                            ) continue;
        if ( file_exists ( PT . $padUnreadName )                                  ) continue;
        if ( isset ( $GLOBALS ['pqStore'] [$padUnreadName] )                      ) continue;
        if ( file_exists ( PAD . "sequence/options/types/$padUnreadName.php" )    ) continue;
        if ( in_array ( $padUnreadName, [ 'sequence', 'pull', 'action' ] )        ) continue;
      }

      return padError ( "the tag {" . $padTag [$pad] . "} was given an option '" . $padUnreadName . "' that nothing reads" );

    }

?>