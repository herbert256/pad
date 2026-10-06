<?php

  // Settles which store a {pull} that named none by prefix, tag or option reads.
  //
  // Its first parameter is the store's name, {pull 'myStore'}, as the tag is documented:
  // nothing read it, so the store pushed last was pulled whatever name was written - two
  // pushes, and {pull 'first'} gave the second. A name that is no store reaches
  // build/types/pull.php, which says so. Without a name it resumes the store that was pushed
  // last, $padLastPush.
  //
  // With nothing pushed and nothing else named there is nothing to resume: the run is
  // pointed at the pull build all the same, which reports that, where it fell through to the
  // default counter and {pull} answered 1 to 10. A pull that names a type or an action as
  // well - {prime:pull}, {reverse:pull} - still runs that over nothing pushed, as before.

  if ( ( $pqTag == 'pull' or $pqType == 'pull' ) and ! $pqPull ) {

    if ( (string) $pqFindParm !== '' ) {
      $pqPull     = $pqFindParm;
      $pqFindParm = '';
    } else
      $pqPull = $padLastPush;

    if ( ! $pqPull and ! $pqSeq and ! $pqAction )
      $pqBuild = 'pull';

  }

?>
