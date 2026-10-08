<?php

  // The job queue: work a page hands off to be done after the request - send the invoice,
  // resize the upload, call the slow API - so the visitor is answered at once and the work
  // is done by a worker, with retries when it fails. What Laravel's queues, Rails' ActiveJob
  // and Celery give, without a broker: the jobs are files under DATA/.
  //
  //   padQueue ( 'sendInvoice', [ 'order' => 42 ] );                  // now
  //   padQueue ( 'reminder', [ 'user' => 7 ], delay: 3600, queue: 'mail' );
  //
  //   _jobs/sendInvoice.php     $order is 42 here - return FALSE, or throw, to fail
  //
  //   pad work shop             the worker: runs the jobs as they come due
  //
  // padQueue          stores a job: the name of its handler, its data, when it may run
  // padQueueSize      the jobs of a queue waiting to run, the delayed ones included
  // padQueueQueues    every queue of the application: [ name => [ due, delayed, running ] ]
  // padQueueWork      runs the jobs of a queue that are due, $max of them (0: all) - and
  //                   answers what it did: [ done, retried, failed, released, jobs ]
  // padQueueFailed    the jobs that failed for good, oldest first
  // padQueueRetry     a failed job back on its queue - one by its id, or 'all'
  // padQueueFlush     removes the failed jobs ('failed'), or the waiting jobs of a queue
  // padQueueRun       runs a handler once, here and now - padQueueWork's and the
  //                   scheduler's (lib/schedule.php): [ ok, error, ms ]
  //
  // A handler is the application's file _jobs/<name>.php. It runs as an _events hook does:
  // in a function scope of its own, the job's data as its local variables, the page's
  // variables through $GLOBALS, what it echoes discarded; $padJob holds the job itself - id,
  // queue, attempts. It runs in the application's context - its _lib functions, its
  // configuration and .env, its database - since a worker is an engine request of the
  // application (pad work, through inits/task.php), and so is the page that calls
  // padQueueWork. A handler that returns FALSE, throws, or raises an error (padError and
  // PHP's warnings alike) has failed this attempt: the error goes to the application's
  // _events/error.php, and the job comes back after its backoff until it has had its
  // tries; then it is moved to the failed jobs, where padQueueRetry can put it back.
  //
  // The store is a directory per application and queue, DATA/queue/<app>/<queue>/: a job
  // is one JSON file, named by the time it may run and its id - 1791000000-0193f2a4c5d6e7a8b9.job
  // - so a sorted listing is the order the jobs come due, and a delayed job is simply one
  // whose time has not come. A worker claims a job by renaming its file to <id>.run: a
  // rename either happens or does not, so of two workers reaching for the same job one gets
  // it and the other moves on - no lock, no job run twice. A .run file whose worker died -
  // killed, or a fatal error PHP cannot catch - is released after its timeout, with the
  // attempt it used counted. The failed jobs are in DATA/queue/<app>/_failed/, <id>.json.
  //
  // The data is JSON: arrays and scalars, keys that are names a variable can have - an
  // object would come back as something else, and a key that is no name would never reach
  // the handler. A replayed request (lib/replay.php) queues nothing and works nothing.

  function padQueue ( $job, $data = [], $delay = 0, $queue = 'default', $tries = 3, $backoff = 10 ) {

    if ( ! padQueueJobName ( $job, 'padQueue' ) or ! padQueueName ( $queue, 'padQueue' ) )
      return FALSE;

    if ( ! str_starts_with ( $job, '@' ) and padQueueHandler ( $job ) === '' ) {
      padError ( "padQueue: there is no job handler _jobs/$job.php in this application" );
      return FALSE;
    }

    if ( ! is_array ( $data ) ) {
      padError ( 'padQueue: the data of a job is an array of named values - not ' . get_debug_type ( $data ) );
      return FALSE;
    }

    foreach ( $data as $name => $value )
      if ( ! is_string ( $name ) or ! padValidVar ( $name ) ) {
        padError ( "padQueue: '" . padMakeSafe ( (string) $name, 40 ) . "' is no name a variable of the handler can have" );
        return FALSE;
      }

    $refused = padCacheAppRefused ( $data );

    if ( $refused !== '' ) {
      padError ( "padQueue: the data of a job is arrays and scalars - it holds $refused" );
      return FALSE;
    }

    if ( ! is_int ( $tries ) or $tries < 1 ) {
      padError ( 'padQueue: tries is a whole number, 1 or more - not ' . padCacheAppShow ( $tries ) );
      return FALSE;
    }

    if ( padQueueBackoff ( $backoff, 1 ) === NULL ) {
      padError ( 'padQueue: the backoff is a number of seconds, or a list of them - one per retry' );
      return FALSE;
    }

    $available = padQueueAvailable ( $delay );

    if ( $available === NULL )
      return FALSE;

    $id = padQueueId ();

    if ( padReplaying () )
      return $id;

    $record = [ 'id'        => $id,
                'job'       => $job,
                'queue'     => $queue,
                'data'      => $data,
                'attempts'  => 0,
                'tries'     => $tries,
                'backoff'   => $backoff,
                'created'   => time (),
                'available' => $available,
                'error'     => '' ];

    if ( ! padQueueWrite ( padQueueDir ( $queue ) . padQueueFileName ( $available, $id ), $record ) )
      return FALSE;

    return $id;

  }

  function padQueueSize ( $queue = 'default' ) {

    if ( ! padQueueName ( $queue, 'padQueueSize' ) )
      return 0;

    return count ( glob ( padQueueDir ( $queue ) . '*.job' ) ?: [] );

  }

  function padQueueQueues () {

    $list = [];
    $now  = time ();

    foreach ( glob ( padQueueAppDir () . '*', GLOB_ONLYDIR ) ?: [] as $dir ) {

      $name = basename ( $dir );

      if ( ! padQueueNameValid ( $name ) )
        continue;

      $due = $delayed = 0;

      foreach ( glob ( "$dir/*.job" ) ?: [] as $file )
        if ( (int) substr ( basename ( $file ), 0, 10 ) <= $now )
          $due++;
        else
          $delayed++;

      $list [$name] = [ 'due' => $due, 'delayed' => $delayed, 'running' => count ( glob ( "$dir/*.run" ) ?: [] ) ];

    }

    ksort ( $list );

    return $list;

  }

  // $tries, when given, stands for every job's own - pad work --tries. $timeout is the
  // longest a job may run: under the command line with pcntl it is stopped then, as a
  // failed attempt, and a job claimed longer ago than that by a worker that is gone is
  // released.

  function padQueueWork ( $queue = 'default', $max = 0, $timeout = 60, $tries = NULL ) {

    $answer = [ 'done' => 0, 'retried' => 0, 'failed' => 0, 'released' => 0, 'jobs' => [] ];

    if ( ! padQueueName ( $queue, 'padQueueWork' ) )
      return $answer;

    if ( ! is_int ( $max ) or $max < 0 ) {
      padError ( 'padQueueWork: max is the number of jobs to run, 0 for every one that is due - not ' . padCacheAppShow ( $max ) );
      return $answer;
    }

    if ( padReplaying () )
      return $answer;

    $dir     = padQueueDir ( $queue );
    $timeout = max ( 1, (int) $timeout );

    $answer ['released'] = padQueueRelease ( $dir, $timeout );

    while ( ! $max or count ( $answer ['jobs'] ) < $max ) {

      $claimed = padQueueClaim ( $dir );

      if ( ! $claimed )
        break;

      [ $run, $record ] = $claimed;

      $result = padQueueRun ( $record ['job'], $record ['data'], $timeout, $record );
      $status = padQueueAfter ( $dir, $run, $record, $result, $tries );

      $answer [$status] ++;

      $answer ['jobs'] [] = [ 'id'       => $record ['id'],
                              'job'      => $record ['job'],
                              'status'   => $status,
                              'attempts' => $record ['attempts'],
                              'ms'       => $result ['ms'],
                              'error'    => $result ['error'] ];

    }

    return $answer;

  }

  function padQueueFailed () {

    $list = [];

    foreach ( glob ( padQueueAppDir () . '_failed/*.json' ) ?: [] as $file ) {

      $record = padQueueRead ( $file );

      if ( $record )
        $list [] = $record;

    }

    return $list;

  }

  // The number of jobs put back: a retried job starts its tries afresh, due at once.

  function padQueueRetry ( $id ) {

    if ( $id !== 'all' and ! padQueueIdValid ( $id ) ) {
      padError ( "padQueueRetry: a job is named by its id, or 'all' - not " . padCacheAppShow ( $id ) );
      return 0;
    }

    if ( padReplaying () )
      return 0;

    $files = ( $id === 'all' ) ? ( glob ( padQueueAppDir () . '_failed/*.json' ) ?: [] )
                               : [ padQueueAppDir () . "_failed/$id.json" ];
    $count = 0;

    foreach ( $files as $file ) {

      $record = is_file ( $file ) ? padQueueRead ( $file ) : NULL;

      if ( ! $record or ! padQueueNameValid ( $record ['queue'] ?? '' ) )
        continue;

      $record ['attempts']  = 0;
      $record ['available'] = time ();

      unset ( $record ['failed'] );

      if ( padQueueWrite ( padQueueDir ( $record ['queue'] ) . padQueueFileName ( $record ['available'], $record ['id'] ), $record ) ) {
        @unlink ( $file );
        $count++;
      }

    }

    return $count;

  }

  // 'failed' empties the failed jobs; a queue's name its waiting jobs - not the ones a
  // worker is running. Answers the number of jobs removed.

  function padQueueFlush ( $what = 'failed' ) {

    if ( $what === 'failed' )
      $pattern = padQueueAppDir () . '_failed/*.json';
    elseif ( padQueueName ( $what, 'padQueueFlush' ) )
      $pattern = padQueueDir ( $what ) . '*.job';
    else
      return 0;

    if ( padReplaying () )
      return 0;

    $count = 0;

    foreach ( glob ( $pattern ) ?: [] as $file )
      if ( @unlink ( $file ) )
        $count++;

    return $count;

  }

  // One run of a handler. '@mail' is the engine's own job, a mail padMail queued
  // (lib/mail.php); every other name is the application's _jobs/<name>.php.
  //
  // While the handler runs, padError throws instead of ending the request, as it does in
  // the _events/error.php hook (padErrorHook), and PHP's warnings throw as well: a failing
  // handler is one failed attempt, and the worker goes on with the next job.

  function padQueueRun ( $job, $data, $timeout = 0, $record = [] ) {

    global $padEventErrorBusy;

    $start = hrtime ( TRUE );
    $ok    = FALSE;
    $error = '';
    $where = [ '', 0 ];

    $file = str_starts_with ( (string) $job, '@' ) ? '' : padQueueHandler ( (string) $job );

    if ( $file === '' and $job !== '@mail' )
      return [ 'ok' => FALSE, 'error' => "there is no job handler _jobs/$job.php", 'ms' => 0 ];

    $busy               = $padEventErrorBusy ?? FALSE;
    $padEventErrorBusy  = TRUE;
    $alarm              = padQueueAlarm ( $timeout );

    set_error_handler ( 'padErrorThrow' );
    ob_start ();

    try {

      if ( $job === '@mail' )
        $result = padMail ( $data ['to'] ?? '', $data ['template'] ?? '', $data ['subject'] ?? '',
                            (array) ( $data ['vars'] ?? [] ), (array) ( $data ['options'] ?? [] ) );
      else
        $result = padQueueInclude ( $file, $data, $record );

      $ok    = ( $result !== FALSE );
      $error = $ok ? '' : 'the handler returned FALSE';

    } catch ( Throwable $e ) {

      $error = preg_replace ( '/^PAD: /', '', $e->getMessage () );
      $where = [ $e->getFile (), $e->getLine () ];

    } finally {

      if ( $alarm )
        pcntl_alarm ( 0 );

      ob_end_clean ();
      restore_error_handler ();

      $padEventErrorBusy = $busy;

    }

    $ms = round ( ( hrtime ( TRUE ) - $start ) / 1e6, 1 );

    if ( ! $ok )
      padEventError ( "PAD: the job '$job' failed: $error", $where [0] ?: ( $file ?: __FILE__ ), $where [1] );

    return [ 'ok' => $ok, 'error' => $error, 'ms' => $ms ];

  }

  function padQueueInclude ( $padQueueFile, $padQueueData, $padJob ) {

    extract ( $padQueueData, EXTR_SKIP );

    unset ( $padJob ['data'] );

    return include $padQueueFile;

  }

  // The job's timeout as an alarm, where pcntl can give one: the handler is stopped by an
  // exception between two statements of its PHP. TRUE when an alarm was set.

  function padQueueAlarm ( $timeout ) {

    if ( PHP_SAPI !== 'cli' or $timeout <= 0 or ! function_exists ( 'pcntl_alarm' ) )
      return FALSE;

    pcntl_async_signals ( TRUE );

    pcntl_signal ( SIGALRM, function () use ( $timeout ) {
      throw new RuntimeException ( "the job ran longer than its timeout of $timeout seconds" );
    } );

    pcntl_alarm ( (int) $timeout );

    return TRUE;

  }

  // The first job of the directory that is due, claimed: [ run file, record ], or NULL when
  // none is. The names sort by the time a job may run, so the first that is not due ends
  // the look. A rename that fails is another worker's claim of the same job.

  function padQueueClaim ( $dir ) {

    $now = time ();

    foreach ( glob ( $dir . '*.job' ) ?: [] as $file ) {

      $name = basename ( $file );

      if ( ! preg_match ( '/^(\d{10})-([0-9a-f]{18})\.job$/D', $name, $match ) )
        continue;

      if ( (int) $match [1] > $now )
        break;

      $run = $dir . $match [2] . '.run';

      if ( ! @rename ( $file, $run ) )
        continue;

      $record = padQueueRead ( $run );

      if ( ! $record ) {
        @unlink ( $run );
        continue;
      }

      $record ['attempts'] = (int) ( $record ['attempts'] ?? 0 ) + 1;
      $record ['reserved'] = $now;

      @file_put_contents ( $run, padQueueJson ( $record ), LOCK_EX );

      return [ $run, $record ];

    }

    return NULL;

  }

  // After a run: a job done is removed, a failed one goes back after its backoff or, its
  // tries spent, to the failed jobs. Answers 'done', 'retried' or 'failed'.

  function padQueueAfter ( $dir, $run, $record, $result, $tries = NULL ) {

    if ( $result ['ok'] ) {
      @unlink ( $run );
      return 'done';
    }

    $record ['error'] = $result ['error'];

    return padQueueFail ( $dir, $run, $record, $tries );

  }

  function padQueueFail ( $dir, $run, $record, $tries = NULL ) {

    $tries = $tries ?? (int) ( $record ['tries'] ?? 1 );

    unset ( $record ['reserved'] );

    if ( $record ['attempts'] < $tries ) {

      $record ['available'] = time () + padQueueBackoff ( $record ['backoff'] ?? 0, $record ['attempts'] );

      if ( padQueueWrite ( $dir . padQueueFileName ( $record ['available'], $record ['id'] ), $record ) ) {
        @unlink ( $run );
        return 'retried';
      }

    }

    $record ['failed'] = time ();

    if ( padQueueWrite ( padQueueAppDir () . '_failed/' . $record ['id'] . '.json', $record ) )
      @unlink ( $run );

    return 'failed';

  }

  // The .run files of workers that are gone: claimed longer ago than the timeout and a
  // margin. Each is claimed back by a rename before it is judged, so two workers releasing
  // at once release it once. Its attempt counts - it may well be the job that killed the
  // worker.

  function padQueueRelease ( $dir, $timeout ) {

    $count = 0;
    $limit = time () - $timeout - 30;

    foreach ( glob ( $dir . '*.run' ) ?: [] as $run ) {

      clearstatcache ( TRUE, $run );

      $time = @filemtime ( $run );

      if ( $time === FALSE or $time > $limit )
        continue;

      $mine = $run . '.' . getmypid ();

      if ( ! @rename ( $run, $mine ) )
        continue;

      $record = padQueueRead ( $mine );

      if ( ! $record ) {
        @unlink ( $mine );
        continue;
      }

      $record ['error'] = 'the worker stopped while it ran the job - it timed out or died';

      padQueueFail ( $dir, $mine, $record );

      $count++;

    }

    return $count;

  }

  // The seconds before retry number $attempt: one number for every retry, or a list with
  // one per retry, its last standing for the ones after. NULL for what is neither.

  function padQueueBackoff ( $backoff, $attempt ) {

    if ( is_array ( $backoff ) and $backoff and array_is_list ( $backoff ) ) {
      foreach ( $backoff as $one )
        if ( ! is_int ( $one ) or $one < 0 )
          return NULL;
      return $backoff [ min ( max ( 0, $attempt - 1 ), count ( $backoff ) - 1 ) ];
    }

    return ( is_int ( $backoff ) and $backoff >= 0 ) ? $backoff : NULL;

  }

  // The time a job may run: now plus a delay in seconds, or a moment. NULL after the fault
  // is reported.

  function padQueueAvailable ( $delay ) {

    if ( $delay instanceof DateTimeInterface )
      return max ( time (), $delay->getTimestamp () );

    if ( $delay instanceof DateInterval )
      return ( new DateTimeImmutable () )->add ( $delay )->getTimestamp ();

    if ( is_int ( $delay ) and $delay >= 0 and $delay < 9999999999 - time () )
      return time () + $delay;

    padError ( 'padQueue: the delay is a number of seconds, 0 or more, a DateTimeInterface or a DateInterval - not '
             . padCacheAppShow ( $delay ) );

    return NULL;

  }

  // The handler of a job, '' when the application has none. The name is checked before it
  // is ever part of a path.

  function padQueueHandler ( $job ) {

    if ( ! preg_match ( '#^[A-Za-z0-9][A-Za-z0-9_-]*(/[A-Za-z0-9][A-Za-z0-9_-]*)*$#D', $job ) )
      return '';

    $file = APP . "_jobs/$job.php";

    return is_file ( $file ) ? $file : '';

  }

  // A job is named as its handler is - letters, digits, _ and -, nested with / - or is one
  // of the engine's own, @mail.

  function padQueueJobName ( $job, $function ) {

    if ( $job === '@mail'
         or ( is_string ( $job ) and preg_match ( '#^[A-Za-z0-9][A-Za-z0-9_-]*(/[A-Za-z0-9][A-Za-z0-9_-]*)*$#D', $job ) ) )
      return TRUE;

    padError ( "$function: a job is named by its handler in _jobs/, like 'sendInvoice' - not " . padCacheAppShow ( $job ) );

    return FALSE;

  }

  function padQueueNameValid ( $queue ) {

    return is_string ( $queue ) and $queue !== 'failed'
           and preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/D', $queue );

  }

  function padQueueName ( $queue, $function ) {

    if ( padQueueNameValid ( $queue ) )
      return TRUE;

    padError ( "$function: a queue is named with letters, digits, _ and - like 'default' or 'mail', and 'failed' is taken - not "
             . padCacheAppShow ( $queue ) );

    return FALSE;

  }

  // Ids sort as they were made: the microseconds since 1970 in hex, and two random bytes
  // for the jobs of one microsecond.

  function padQueueId () {

    return sprintf ( '%014x', (int) ( microtime ( TRUE ) * 1e6 ) ) . bin2hex ( random_bytes ( 2 ) );

  }

  function padQueueIdValid ( $id ) {

    return is_string ( $id ) and preg_match ( '/^[0-9a-f]{18}$/D', $id );

  }

  function padQueueFileName ( $available, $id ) {

    return sprintf ( '%010d', $available ) . "-$id.job";

  }

  function padQueueAppDir () {

    return DATA . 'queue/' . $GLOBALS ['padApp'] . '/';

  }

  function padQueueDir ( $queue ) {

    return padQueueAppDir () . "$queue/";

  }

  function padQueueRead ( $file ) {

    $record = json_decode ( (string) @file_get_contents ( $file ), TRUE );

    return ( is_array ( $record ) and padQueueIdValid ( $record ['id'] ?? '' ) and isset ( $record ['job'] ) )
           ? $record + [ 'data' => [] ] : NULL;

  }

  function padQueueJson ( $record ) {

    return json_encode ( $record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
                                | JSON_INVALID_UTF8_SUBSTITUTE );

  }

  // Written beside its place and renamed over it (padFilePut), so a worker never reads half
  // a job.

  function padQueueWrite ( $file, $record ) {

    return padFilePut ( $file, padQueueJson ( $record ) ) !== FALSE;

  }

?>
