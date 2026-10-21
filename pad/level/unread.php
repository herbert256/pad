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

  // A custom tag that declares its parameters with {parms} is swept too: a parameter it was
  // given and does not declare is reported by name (lib/slot.php).

  $padUnreadDeclared = $padSlot [$pad] ['declared'] ?? NULL;

  if ( $padTagSeq [$pad] or in_array ( $padType [$pad], [ 'pad', 'select' ] ) or $padUnreadDeclared !== NULL )

    foreach ( $padParms [$pad] as $padUnread ) {

      if ( $padUnread ['padPrmKind'] != 'option' )
        continue;

      $padUnreadName = $padUnread ['padPrmName'];

      if ( padIsDone ( $padUnreadName ) )
        continue;

      if ( file_exists ( PAD . "options/$padUnreadName.php" ) )
        continue;

      // A modifier is read by the handler it belongs to - sum, avg, min and max by group,
      // orderly and duplicates by random, left, right and both by trim - and its file under
      // handling/types/ is a placeholder for the reference, which let it pass below on a tag
      // without that handler: {orders sum='total'} written for a grand total rendered the rows
      // unsummed in silence. atLeastOnce belongs to the randomize action of a sequence.

      $padUnreadOwner = [ 'sum' => 'group', 'avg' => 'group', 'min' => 'group', 'max' => 'group',
                          'orderly' => 'random', 'duplicates' => 'random', 'atLeastOnce' => 'randomize',
                          'left' => 'trim', 'right' => 'trim', 'both' => 'trim' ] [$padUnreadName] ?? NULL;

      if ( $padUnreadOwner !== NULL and ! $padTagSeq [$pad] and ! isset ( $padPrm [$pad] [$padUnreadOwner] ) )
        return padError ( "the tag {" . $padTag [$pad] . "} was given an option '$padUnreadName' that nothing reads - "
                          . ( $padUnreadOwner == 'randomize'
                              ? 'the randomize action of a sequence reads it'
                              : "$padUnreadOwner reads it, and the tag has no $padUnreadOwner" ) );

      // A sequence takes its handling from the subsystem, not from handling/ - where=, group=
      // and page= on one did nothing, in silence - so there a handling name is read only when
      // it is a sequence option or action as well (below).

      if ( file_exists ( PAD . "handling/types/$padUnreadName.php" ) and ! $padTagSeq [$pad] )
        continue;

      // A piped tag can carry its pipe functions into the parsed options - {echo $d |
      // date('Y-m-d')} does - and a pipe function is not an unread option.

      if ( file_exists ( PAD . "functions/$padUnreadName.php" ) )
        continue;

      if ( isset ( $padOptionsAppStartCall [$pad] [$padUnreadName] ) )
        continue;

      if ( isset ( $padOptionsAppEndCall [$pad] [$padUnreadName] ) )
        continue;

      // A sequence tag's words may also be its actions, its types, its stores, the
      // min/max option pair, or the subsystem's own mode words - readers of their own.

      if ( $padTagSeq [$pad] ) {
        if ( pqAction ( $padUnreadName )                                          ) continue;
        if ( pqSeq ( $padUnreadName )                                             ) continue;
        if ( isset ( $GLOBALS ['pqStore'] [$padUnreadName] )                      ) continue;
        if ( pqOption ( $padUnreadName, $padPrm [$pad] [$padUnreadName] ?? TRUE ) ) continue;
        if ( in_array ( $padUnreadName, [ 'sequence', 'pull', 'action' ] )        ) continue;
      }

      if ( $padUnreadDeclared !== NULL )
        return padError ( "the tag {" . $padTag [$pad] . "} has no parameter '" . $padUnreadName . "' - it declares "
                          . ( $padUnreadDeclared ? implode ( ', ', $padUnreadDeclared ) : 'none' ) );

      return padError ( "the tag {" . $padTag [$pad] . "} was given an option '" . $padUnreadName . "' that nothing reads" );

    }

?>
