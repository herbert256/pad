<?php

  // The task scheduler: an application says in one file what runs when - the nightly
  // cleanup, the reminders every Monday, a feed fetched every five minutes - and one cron
  // line on the server drives every application. What Laravel's scheduler, whenever and
  // django-crontab give.
  //
  //   _schedule.php, which returns the list:
  //     return [
  //       [ 'every' => '5 minutes',        'job' => 'fetchFeed' ],
  //       [ 'every' => 'day at 03:00',     'job' => 'cleanup', 'data' => [ 'days' => 30 ] ],
  //       [ 'cron'  => '0 8 * * mon-fri',  'job' => 'digest',  'queue' => TRUE ],
  //       [ 'every' => 'minute',           'job' => 'sync',    'overlap' => FALSE ],
  //     ];
  //
  //   * * * * *  /path/to/pad/apps/cli/pad schedule --all
  //
  // An entry names a job - the application's _jobs/<name>.php, the handler a queued job has
  // (lib/queue.php) - with its data, and when it runs: 'cron', five fields, or 'every', the
  // words for one: minute, 5 minutes, hour, hour at :15, 3 hours, day, day at 03:00,
  // weekday at 08:00, monday at 08:00, week, month, month at 06:00, year. It runs in the
  // scheduler's own process (inline, the default) or is handed to the queue ('queue' =>
  // TRUE, or a queue's name) for a worker. 'overlap' => FALSE keeps it from starting while
  // its last run is still going: an inline run holds a lock file, a queued one is not queued
  // again while the job it queued is waiting or running. 'name' names an entry in the list
  // and the record; the job is its name otherwise, the job and its time when the job is
  // there twice.
  //
  // padCronMatch    whether a cron expression matches a moment, to the minute
  // padCronNext     the first moment after a given one that a cron expression matches
  // padCronEvery    the 'every' words as a cron expression
  // padScheduleDue  whether an entry runs at a moment
  // padScheduleList the application's entries, each with its cron, next and last run
  // padScheduleRun  runs the entries that are due this minute, once each
  //
  // The cron syntax is the usual five fields - minute, hour, day of the month, month, day
  // of the week - each a *, a number, a range a-b, a step */n or a-b/n, or a list of these
  // with commas; months jan-dec and days sun-sat by name, Sunday 0 or 7; @hourly, @daily,
  // @midnight, @weekly, @monthly, @yearly and @annually. As cron has it, a day of the month
  // and a day of the week that are both restricted match when either does. The moments are
  // read in the application's timezone ($padTimezone, else PHP's).
  //
  // The scheduler is meant to be asked once a minute. What ran, and when, is kept in
  // DATA/schedule/<app>.json - an entry runs once in a minute however often it is asked,
  // and pad schedule --list shows the last run beside the next - and the lock files of
  // the entries that may not overlap stand beside it, <app>.<hash of the name>.lock.

  function padCronMatch ( $expr, $time = NULL ) {

    $fields = padCronParse ( $expr, 'padCronMatch' );

    if ( ! $fields )
      return FALSE;

    return padCronFits ( $fields, padCronMoment ( $time ) );

  }

  // NULL when the expression matches no moment in the next five years - 0 0 30 2 *.

  function padCronNext ( $expr, $time = NULL ) {

    $fields = padCronParse ( $expr, 'padCronNext' );

    if ( ! $fields )
      return NULL;

    $at = padCronMoment ( $time );
    $at = $at->setTime ( (int) $at->format ( 'G' ), (int) $at->format ( 'i' ) )->modify ( '+1 minute' );

    $end = $at->modify ( '+5 years' );

    while ( $at < $end ) {

      if ( ! isset ( $fields ['month'] [ (int) $at->format ( 'n' ) ] ) ) {
        $at = $at->modify ( 'first day of next month' )->setTime ( 0, 0 );
        continue;
      }

      if ( ! padCronDay ( $fields, $at ) ) {
        $at = $at->modify ( '+1 day' )->setTime ( 0, 0 );
        continue;
      }

      if ( ! isset ( $fields ['hour'] [ (int) $at->format ( 'G' ) ] ) ) {
        $at = $at->setTime ( (int) $at->format ( 'G' ), 0 )->modify ( '+1 hour' );
        continue;
      }

      if ( ! isset ( $fields ['minute'] [ (int) $at->format ( 'i' ) ] ) ) {
        $at = $at->modify ( '+1 minute' );
        continue;
      }

      return $at->getTimestamp ();

    }

    return NULL;

  }

  // The 'every' words: [count] unit [at time]. Answers '' after the fault is reported.

  function padCronEvery ( $every ) {

    $days  = [ 'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
               'thursday' => 4, 'friday' => 5, 'saturday' => 6 ];
    $text  = strtolower ( trim ( preg_replace ( '/\s+/', ' ', (string) $every ) ) );
    $units = 'minute|hour|day|weekday|week|month|year|' . implode ( '|', array_keys ( $days ) );

    if ( ! preg_match ( "/^(?:(\d+) )?($units)s?(?: at (?:(\d{1,2}):(\d{2})|:(\d{2})))?$/D", $text, $m ) ) {
      padError ( "padCronEvery: '" . padMakeSafe ( (string) $every, 40 ) . "' is no time to run - minute, 5 minutes, hour, day at 03:00, monday at 08:00, month ..." );
      return '';
    }

    $count  = ( $m [1] ?? '' ) === '' ? 1 : (int) $m [1];
    $unit   = $m [2];
    $hour   = ( $m [3] ?? '' ) === '' ? 0 : (int) $m [3];
    $minute = ( $m [4] ?? '' ) !== '' ? (int) $m [4] : ( ( $m [5] ?? '' ) !== '' ? (int) $m [5] : 0 );
    $hasAt  = ( $m [3] ?? '' ) !== '' or ( $m [5] ?? '' ) !== '';
    $clock  = ( $m [3] ?? '' ) !== '';

    $fault = function ( $why ) use ( $every ) {
      padError ( "padCronEvery: '" . padMakeSafe ( (string) $every, 40 ) . "' - $why" );
      return '';
    };

    if ( $hour > 23 or $minute > 59 )
      return $fault ( 'the time of day is hh:mm, 00:00 to 23:59' );

    if ( $count < 1 )
      return $fault ( 'the count is 1 or more' );

    if ( $count > 1 and ! in_array ( $unit, [ 'minute', 'hour' ] ) )
      return $fault ( 'a count goes with minutes and hours - say it in cron for the others' );

    if ( $unit == 'minute' and $hasAt )
      return $fault ( 'a minute has no time of day' );

    if ( $unit == 'hour' and $clock )
      return $fault ( "an hour is at a minute past it - 'hour at :15'" );

    if ( $unit != 'hour' and ! $clock and $hasAt )
      return $fault ( "the time of day is hh:mm - 'day at 03:00'" );

    if ( $count > 1 and ( ( $unit == 'minute' and 60 % $count ) or ( $unit == 'hour' and 24 % $count ) ) )
      return $fault ( 'the count divides ' . ( $unit == 'minute' ? 'an hour (60)' : 'a day (24)' ) . ' - cron restarts it every '
                    . ( $unit == 'minute' ? 'hour' : 'day' ) );

    switch ( $unit ) {
      case 'minute':  return $count > 1 ? "*/$count * * * *" : '* * * * *';
      case 'hour':    return $count > 1 ? "$minute */$count * * *" : "$minute * * * *";
      case 'day':     return "$minute $hour * * *";
      case 'weekday': return "$minute $hour * * 1-5";
      case 'week':    return "$minute $hour * * 0";
      case 'month':   return "$minute $hour 1 * *";
      case 'year':    return "$minute $hour 1 1 *";
      default:        return "$minute $hour * * " . $days [$unit];
    }

  }

  // An entry's cron expression - its 'cron', or its 'every' in cron - '' after the fault is
  // reported.

  function padScheduleCron ( $entry ) {

    if ( ! is_array ( $entry ) ) {
      padError ( 'padSchedule: an entry is an array - [ \'every\' => \'day\', \'job\' => \'cleanup\' ] - not ' . get_debug_type ( $entry ) );
      return '';
    }

    if ( isset ( $entry ['cron'] ) and isset ( $entry ['every'] ) ) {
      padError ( "padSchedule: an entry has 'cron' or 'every', not both" );
      return '';
    }

    if ( isset ( $entry ['every'] ) )
      return padCronEvery ( $entry ['every'] );

    if ( isset ( $entry ['cron'] ) )
      return padCronParse ( $entry ['cron'], 'padSchedule' ) ? trim ( (string) $entry ['cron'] ) : '';

    padError ( "padSchedule: an entry needs 'cron' or 'every' to say when it runs" );

    return '';

  }

  function padScheduleDue ( $entry, $time = NULL ) {

    $cron = padScheduleCron ( $entry );

    return ( $cron !== '' ) and padCronMatch ( $cron, $time );

  }

  // The application's entries, checked, under their names: [ name => [ name, job, cron,
  // data, queue, overlap, next, last ] ]. An entry with a fault is reported and left out.

  function padScheduleList ( $time = NULL ) {

    $file = APP . '_schedule.php';

    if ( ! is_file ( $file ) )
      return [];

    $entries = padScheduleLoad ( $file );

    if ( ! is_array ( $entries ) ) {
      padError ( '_schedule.php returns the list of entries - return [ [ \'every\' => \'day\', \'job\' => \'cleanup\' ] ];' );
      return [];
    }

    $state = padScheduleState ();
    $list  = [];
    $jobs  = array_count_values ( array_map ( fn ( $one ) => is_array ( $one ) ? (string) ( $one ['job'] ?? '' ) : '', $entries ) );

    foreach ( $entries as $entry ) {

      $cron = padScheduleCron ( $entry );

      if ( $cron === '' )
        continue;

      $job = $entry ['job'] ?? '';

      if ( ! padQueueJobName ( $job, 'padSchedule' ) )
        continue;

      if ( padQueueHandler ( $job ) === '' ) {
        padError ( "padSchedule: there is no job handler _jobs/$job.php in this application" );
        continue;
      }

      $queue = $entry ['queue'] ?? FALSE;

      if ( $queue === TRUE )
        $queue = 'default';

      if ( $queue !== FALSE and ! padQueueName ( $queue, 'padSchedule' ) )
        continue;

      $data = $entry ['data'] ?? [];

      if ( ! is_array ( $data ) ) {
        padError ( "padSchedule: the data of the entry for '$job' is an array of named values" );
        continue;
      }

      $name = (string) ( $entry ['name'] ?? ( $jobs [$job] > 1 ? "$job $cron" : $job ) );

      if ( isset ( $list [$name] ) ) {
        padError ( "padSchedule: two entries are named '" . padMakeSafe ( $name, 40 ) . "' - give one a 'name'" );
        continue;
      }

      $list [$name] = [ 'name'    => $name,
                        'job'     => $job,
                        'cron'    => $cron,
                        'data'    => $data,
                        'queue'   => $queue,
                        'overlap' => ( $entry ['overlap'] ?? TRUE ) !== FALSE,
                        'next'    => padCronNext ( $cron, $time ),
                        'last'    => $state [$name] ?? NULL ];

    }

    return $list;

  }

  function padScheduleLoad ( $padScheduleFile ) {

    return include $padScheduleFile;

  }

  // Runs what is due at $time (now when NULL), each entry once in its minute: an entry the
  // record says ran in this minute already is passed by. Answers a line per entry that was
  // due: [ name, job, status, ms, error, id ] - status 'done', 'failed', 'queued', 'skipped'
  // (overlap) or 'ran' (this minute already).

  function padScheduleRun ( $time = NULL ) {

    $moment = padCronMoment ( $time );
    $minute = intdiv ( $moment->getTimestamp (), 60 ) * 60;
    $ran    = [];

    if ( padReplaying () )
      return $ran;

    foreach ( padScheduleList ( $time ) as $name => $entry ) {

      if ( ! padCronMatch ( $entry ['cron'], $moment ) )
        continue;

      $line = [ 'name' => $name, 'job' => $entry ['job'], 'status' => '', 'ms' => 0, 'error' => '', 'id' => '' ];

      // The record is read again for each entry: a run of another process may have written
      // it while an earlier entry of this one ran.

      $last = padScheduleState () [$name] ?? NULL;

      if ( $last and ( $last ['minute'] ?? 0 ) == $minute ) {
        $ran [] = [ 'status' => 'ran' ] + $line;
        continue;
      }

      if ( $entry ['queue'] !== FALSE )
        $line = padScheduleQueue ( $entry, $last, $line );
      else
        $line = padScheduleInline ( $entry, $line );

      padScheduleRecord ( $name, [ 'minute' => $minute,
                                   'time'   => time (),
                                   'status' => $line ['status'],
                                   'ms'     => $line ['ms'],
                                   'error'  => $line ['error'],
                                   'id'     => $line ['id'] ?: ( $last ['id'] ?? '' ) ] );

      $ran [] = $line;

    }

    return $ran;

  }

  // Inline: the handler runs here, under the entry's lock when it may not overlap - a lock
  // another process holds means its last run is still going.

  function padScheduleInline ( $entry, $line ) {

    $lock = NULL;

    if ( ! $entry ['overlap'] ) {

      $file = padScheduleDir () . $GLOBALS ['padApp'] . '.' . md5 ( $entry ['name'] ) . '.lock';

      if ( ! is_dir ( dirname ( $file ) ) )
        @mkdir ( dirname ( $file ), $GLOBALS ['padDirMode'] ?? 0755, TRUE );

      $lock = @fopen ( $file, 'c' );

      if ( $lock and ! flock ( $lock, LOCK_EX | LOCK_NB ) ) {
        fclose ( $lock );
        return [ 'status' => 'skipped', 'error' => 'its last run is still going' ] + $line;
      }

    }

    try {

      $result = padQueueRun ( $entry ['job'], $entry ['data'] );

    } finally {

      if ( $lock ) {
        flock  ( $lock, LOCK_UN );
        fclose ( $lock );
      }

    }

    return [ 'status' => $result ['ok'] ? 'done' : 'failed', 'ms' => $result ['ms'], 'error' => $result ['error'] ] + $line;

  }

  // Queued: one more job, unless the entry may not overlap and the job it queued last is
  // still waiting or running.

  function padScheduleQueue ( $entry, $last, $line ) {

    $id = $last ['id'] ?? '';

    if ( ! $entry ['overlap'] and padQueueIdValid ( $id )
         and ( glob ( padQueueDir ( $entry ['queue'] ) . "*$id.*" ) ?: [] ) )
      return [ 'status' => 'skipped', 'error' => "the job it queued last, $id, is still on the queue" ] + $line;

    $id = padQueue ( $entry ['job'], $entry ['data'], queue: $entry ['queue'] );

    if ( $id === FALSE )
      return [ 'status' => 'failed', 'error' => 'it could not be queued' ] + $line;

    return [ 'status' => 'queued', 'id' => $id ] + $line;

  }

  function padScheduleDir () {

    return DATA . 'schedule/';

  }

  function padScheduleFile () {

    return padScheduleDir () . $GLOBALS ['padApp'] . '.json';

  }

  function padScheduleState () {

    $file  = padScheduleFile ();
    $state = is_file ( $file ) ? json_decode ( (string) @file_get_contents ( $file ), TRUE ) : [];

    return is_array ( $state ) ? $state : [];

  }

  // The record is read, changed and written under a lock, so two entries ending at once in
  // two processes both stay in it.

  function padScheduleRecord ( $name, $run ) {

    $file = padScheduleFile ();
    $dir  = dirname ( $file );

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, $GLOBALS ['padDirMode'] ?? 0755, TRUE );

    $lock = @fopen ( "$file.lock", 'c' );

    if ( $lock )
      flock ( $lock, LOCK_EX );

    try {

      $state          = padScheduleState ();
      $state [$name]  = $run;

      ksort ( $state );

      padFilePut ( $file, json_encode ( $state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                                              | JSON_INVALID_UTF8_SUBSTITUTE ) );

    } finally {

      if ( $lock ) {
        flock  ( $lock, LOCK_UN );
        fclose ( $lock );
      }

    }

  }

  // A moment as a DateTimeImmutable in the application's timezone: NULL is now (padNow - a
  // frozen clock holds), a number a Unix timestamp, a DateTimeInterface itself.

  function padCronMoment ( $time ) {

    if ( $time === NULL )
      return padNow ();

    if ( $time instanceof DateTimeInterface )
      return DateTimeImmutable::createFromInterface ( $time )->setTimezone ( padDateZone () );

    return ( new DateTimeImmutable ( '@' . (int) $time ) )->setTimezone ( padDateZone () );

  }

  function padCronFits ( $fields, $at ) {

    return isset ( $fields ['minute'] [ (int) $at->format ( 'i' ) ] )
       and isset ( $fields ['hour']   [ (int) $at->format ( 'G' ) ] )
       and isset ( $fields ['month']  [ (int) $at->format ( 'n' ) ] )
       and padCronDay ( $fields, $at );

  }

  // The day of the month and the day of the week: both restricted, either will do - cron's
  // own rule, by which 0 0 1 * mon runs on the first and on every Monday.

  function padCronDay ( $fields, $at ) {

    $dom = isset ( $fields ['dom'] [ (int) $at->format ( 'j' ) ] );
    $dow = isset ( $fields ['dow'] [ (int) $at->format ( 'w' ) ] );

    if ( $fields ['domAll'] or $fields ['dowAll'] )
      return $dom and $dow;

    return $dom or $dow;

  }

  // The five fields as sets - [ 'minute' => [ 0 => TRUE, 15 => TRUE ], ... ] - with whether
  // the two day fields are *. FALSE after the fault is reported. Kept for the request: a
  // scheduler asks the same few expressions over and over.

  function padCronParse ( $expr, $function ) {

    static $parsed = [];

    $aliases = [ '@yearly'  => '0 0 1 1 *', '@annually' => '0 0 1 1 *', '@monthly'  => '0 0 1 * *',
                 '@weekly'  => '0 0 * * 0', '@daily'    => '0 0 * * *', '@midnight' => '0 0 * * *',
                 '@hourly'  => '0 * * * *' ];

    if ( ! is_string ( $expr ) ) {
      padError ( "$function: a cron expression is text, five fields like '*/5 * * * *' - not " . get_debug_type ( $expr ) );
      return FALSE;
    }

    $text = strtolower ( trim ( preg_replace ( '/\s+/', ' ', $expr ) ) );
    $text = $aliases [$text] ?? $text;

    if ( isset ( $parsed [$text] ) )
      return $parsed [$text];

    $parts = explode ( ' ', $text );

    if ( count ( $parts ) != 5 ) {
      padError ( "$function: '" . padMakeSafe ( $expr, 40 ) . "' is no cron expression - five fields: minute hour day month weekday" );
      return FALSE;
    }

    $months = [ 'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
                'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12 ];
    $days   = [ 'sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6 ];

    $specs = [ 'minute' => [ 0, 59, [] ], 'hour'  => [ 0, 23, []      ], 'dom' => [ 1, 31, [] ],
               'month'  => [ 1, 12, $months ], 'dow' => [ 0, 7, $days ] ];

    $fields = [];
    $i      = 0;

    foreach ( $specs as $name => [ $low, $high, $names ] ) {

      $set = padCronField ( $parts [$i++], $low, $high, $names );

      if ( $set === NULL ) {
        padError ( "$function: '" . padMakeSafe ( $expr, 40 ) . "' - the $name field '" . padMakeSafe ( $parts [$i-1], 20 )
                 . "' is no list of values from $low to $high" );
        return FALSE;
      }

      $fields [$name] = $set;

    }

    if ( isset ( $fields ['dow'] [7] ) )
      $fields ['dow'] [0] = TRUE;

    // A day field that starts with * is no restriction for the either-or rule - */2 too, as
    // cron itself reads it.

    $fields ['domAll'] = str_starts_with ( $parts [2], '*' );
    $fields ['dowAll'] = str_starts_with ( $parts [4], '*' );

    if ( count ( $parsed ) > 256 )
      $parsed = [];

    return $parsed [$text] = $fields;

  }

  // One field as a set of the values it allows, NULL for a field that is none.

  function padCronField ( $field, $low, $high, $names ) {

    $set = [];

    foreach ( explode ( ',', $field ) as $item ) {

      if ( ! preg_match ( '#^(\*|([a-z0-9]+)(?:-([a-z0-9]+))?)(?:/(\d+))?$#D', $item, $m ) )
        return NULL;

      $step = isset ( $m [4] ) ? (int) $m [4] : 1;

      if ( $step < 1 )
        return NULL;

      if ( $m [1] === '*' ) {
        $from = $low;
        $to   = $high;
      } else {
        $from = padCronValue ( $m [2], $names );
        $to   = ( ( $m [3] ?? '' ) !== '' ) ? padCronValue ( $m [3], $names ) : ( isset ( $m [4] ) ? $high : $from );
      }

      if ( $from === NULL or $to === NULL or $from < $low or $to > $high or $from > $to )
        return NULL;

      for ( $value = $from; $value <= $to; $value += $step )
        $set [$value] = TRUE;

    }

    return $set;

  }

  function padCronValue ( $text, $names ) {

    if ( ctype_digit ( $text ) )
      return (int) $text;

    return $names [$text] ?? NULL;

  }

?>
