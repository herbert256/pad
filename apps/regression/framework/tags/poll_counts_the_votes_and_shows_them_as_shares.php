<?php

  // A fresh store for every run: the votes below are all there is.

  @unlink ( padPollFile ( 'fw-shares' ) );

  foreach ( [ 'PHP', 'PHP', 'Go', 'PHP', 'Elm' ] as $vote )
    padPollVote ( 'fw-shares', $vote );

?>
