<?php

  // Shared parameter check for the types that do arithmetic with theirs: reports a parameter
  // that is not a number, so {sequence multiply='abc'} says so instead of ending the request
  // on "Unsupported operand types" from inside the type.
  //
  // Included from a type's own init.php, which is run once per build - by inits/init.php for
  // the sequence a tag generates, and by plays/init.php for one used as a play - so the
  // answer is given once however many candidates follow.
  //
  // A type used without a parameter at all is left alone: the parameter is TRUE for a bare
  // option and the types read that as the 1 they default to, which is the behaviour they
  // have always had. A type with a further rule of its own - divide cannot take 0 - adds it
  // after including this.

  // An empty parameter is one not given - add=$x with $x empty, sequence:add(3, '') - as an
  // empty rows= is: the bare type's TRUE. Left as it was, the type did its arithmetic with the
  // empty text and ended the request on "Unsupported operand types: int + string", or divide
  // and multiple answered nothing where the bare type counts 1 2 3. plays/init.php hands the
  // TRUE on to the play the parameter belongs to.

  if ( $pqParm === '' or $pqParm === NULL )
    $pqParm = TRUE;

  if ( $pqParm === TRUE )
    return;

  if ( is_numeric ( $pqParm ) )
    return;

  // A list is no number either: str_contains () ended the request on its TypeError. The
  // lenient walk carries on with the bare type.

  if ( ! is_scalar ( $pqParm ) ) {
    padError ( "The $pqSeq sequence needs a number as its parameter, not a list" );
    $pqParm = TRUE;
    return;
  }

  // Two parameters are not numbers yet and are not the type's to refuse. A range is drawn
  // from once per term by build/parm.php, and a stored sequence hands over one of its terms
  // per term through build/store.php - build/vars.php recognises both, but that runs after
  // this does, so the text is all there is to go on here.

  if ( str_contains ( $pqParm, '..' ) )
    return;

  if ( isset ( $pqStore [$pqParm] ) )
    return;

  padError ( "The $pqSeq sequence needs a number as its parameter, not '$pqParm'" );

?>
