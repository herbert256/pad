<?php

  // Runs {resume}: applies another transformation to the sequence that was pushed last.
  //
  // Same shape as sequence/tag.php but with no name resolution - inits/set.php has already
  // pointed the run at $padLastPush with build 'pull' - and it ends at exits/store/set.php,
  // which writes the new values back over the store rather than publishing them to the level.
  //
  // Returns NULL: {resume} produces no occurrences of its own, it only rewrites the store.

  include PQ . 'inits/tag.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'plays/inits.php';
  include PQ . 'actions/inits.php';
  include PQ . 'inits/limits.php';
  include PQ . 'inits/page.php';
  include PQ . 'build/build.php';
  if ( ! $pqPageCut )
    include PQ . 'exits/page.php';
  include PQ . 'exits/actions.php';
  // The store resumed is the one written back - $padLastPush for exits/store/set.php while it
  // runs - and the store pushed last stays the one it was.

  $pqResumeLast = $padLastPush;
  $padLastPush  = $pqPull;

  include PQ . 'exits/store/set.php';

  // The rows of the store as {resume} leaves it, made as a tag's are and kept by
  // exits/extra/set.php for the next pull to chain in: the rows of the store before the
  // resume stayed, and {resume reverse} gave 16 the columns of 11. The resume tag itself shows
  // nothing, so its level's data is put back as it was.

  $pqResumeData = $padData [$pad] ?? NULL;

  include PQ . 'exits/return/return.php';
  include PQ . 'exits/extra/extra.php';

  $pqResumeRows   = $padData [$pad];
  $padData [$pad] = $pqResumeData;

  // push= and name= keep the terms under a name of their own as well, and toData= publishes
  // them, as on any sequence tag: {resume} took the three without a word and did none of them.
  //
  // With the rows the run shows, as a pull's push= keeps them, and a push= is the store pushed
  // last: the terms alone were kept, {pull:u} after {resume reverse, push='u'} had none of the
  // chained columns - under the strict check "Field '$range' not found" - and a bare {pull}
  // after {resume a, add=10, push='c'} pulled the store pushed before it.

  foreach ( [ $pqPush, $pqNameGiven ] as $pqResumeAt => $pqResumeAs )
    if ( is_string ( $pqResumeAs ) and $pqResumeAs !== '' and $pqResumeAs != $pqPull ) {
      $pqResumeTaken = pqStoreTaken ( $pqResumeAs );
      if ( $pqResumeTaken )
        padError ( "Store name '$pqResumeAs' can not be equal to $pqResumeTaken" );
      else {
        $pqStore    [$pqResumeAs] = array_values ( $pqResult );
        $padSeqData [$pqResumeAs] = $pqResumeRows;
        if ( $pqResumeAt == 0 )
          $pqResumeLast = $pqResumeAs;
      }
    }

  // A push= that names the store resumed, or a bare push, keeps the terms where they are
  // written back already, and makes that store the one pushed last, as push='u' makes u and
  // as {pull:s reverse, push='s'/} makes s: after {sequence '1..5', push='s'} and {sequence
  // '1..3', push='t'}, {resume s, reverse, push='s'} left t the store pushed last, and a bare
  // {pull} gave 1 2 3.

  if ( $pqPush === TRUE or ( is_string ( $pqPush ) and $pqPush !== '' and $pqPush == $pqPull ) )
    $pqResumeLast = $pqPull;

  include PQ . 'exits/data.php';

  $padLastPush = $pqResumeLast;
  include PQ . 'exits/done.php';
  include PQ . 'exits/info.php';

  return NULL;

?>
