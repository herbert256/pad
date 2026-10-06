<?php

  // Build strategy 'pull': the terms come from a sequence stored earlier under a name or
  // pushed by a previous tag. Lifts $pqStore[$pqPull] into $pqFixed and hands it to the
  // fixed iterator, so from/to/plays/actions apply to the stored terms as usual.
  //
  // A store that was never pushed has no terms. {resume} before any push, pull='nope' and
  // sequence:sum('nope') read an undefined key - and sequence:sum() a null one - and ended
  // the request on the PHP warning. The strict check names the miss, as {pull:nope} and
  // pull:nope in an expression do; the run then has nothing to pull and answers nothing.
  // An action called with no values at all, sequence:sum(), simply acts on none.

  if ( ( is_string ( $pqPull ) or is_int ( $pqPull ) ) and isset ( $pqStore [$pqPull] ) )

    $pqFixed = $pqStore [$pqPull];

  else {

    if ( $pqPull !== NULL and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
      padError ( ( is_scalar ( $pqPull ) and (string) $pqPull !== '' )
               ? "there is no stored sequence named '$pqPull'"
               : "there is no stored sequence to pull: nothing has been pushed" );

    $pqFixed = [];

  }

  include PQ . 'build/types/type/fixed.php';

?>
