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

  if ( $pqParm === '' or $pqParm === TRUE or $pqParm === NULL )
    return;

  if ( is_numeric ( $pqParm ) )
    return;

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