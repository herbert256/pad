<?php

  // The votes of a {poll}, kept in DATA/poll/<app>/<name>.json - the part of the tag that a
  // page's PHP uses too, to read the counts or to add a vote of its own; the widget is
  // tags/_lib/poll.php, loaded when a page uses the tag.
  //
  // padPollFile    the file of a poll's votes
  // padPollCounts  the votes per answer, read under a shared lock
  // padPollVote    one vote added, under an exclusive lock - also for a page's PHP or a test

  function padPollFile ( $name ) {

    return DATA . 'poll/' . $GLOBALS ['padApp'] . "/$name.json";

  }

  function padPollCounts ( $name ) {

    $file = padPollFile ( $name );

    if ( ! is_file ( $file ) or ! ( $handle = fopen ( $file, 'r' ) ) )
      return [];

    flock ( $handle, LOCK_SH );
    $json = stream_get_contents ( $handle );
    flock ( $handle, LOCK_UN );
    fclose ( $handle );

    $counts = json_decode ( (string) $json, TRUE );

    return is_array ( $counts ) ? $counts : [];

  }

  function padPollVote ( $name, $answer ) {

    $file = padPollFile ( $name );

    if ( ! is_dir ( dirname ( $file ) ) )
      @mkdir ( dirname ( $file ), 0775, TRUE );

    if ( ! ( $handle = @fopen ( $file, 'c+' ) ) )
      return padError ( "the votes of the poll '$name' cannot be written" );

    flock ( $handle, LOCK_EX );

    $counts = json_decode ( (string) stream_get_contents ( $handle ), TRUE );

    if ( ! is_array ( $counts ) )
      $counts = [];

    $counts [$answer] = ( (int) ( $counts [$answer] ?? 0 ) ) + 1;

    ftruncate ( $handle, 0 );
    rewind    ( $handle );
    fwrite    ( $handle, json_encode ( $counts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
    fflush    ( $handle );
    flock     ( $handle, LOCK_UN );
    fclose    ( $handle );

    return $counts;

  }

?>
