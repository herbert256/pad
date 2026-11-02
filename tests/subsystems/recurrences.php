<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/recurrence.php';
  function check ( $got, $want ) { if ( $got !== $want ) throw new Exception ( json_encode ( [ $got, $want ] ) ); }
  check ( padOccurrences ( 'monthly', [ 'start' => '2026-01-31', 'count' => 3 ], '2026-01-01', '2026-07-01' ), [ '2026-01-31', '2026-03-31', '2026-05-31' ] );
  check ( padOccurrences ( 'yearly', [ 'start' => '2024-02-29', 'count' => 3 ], '2024-01-01', '2033-01-01' ), [ '2024-02-29', '2028-02-29', '2032-02-29' ] );
  check ( padOccurrences ( 'daily', [ 'start' => '2026-03-28T02:30:00', 'timezone' => 'Europe/Amsterdam', 'count' => 3 ], '2026-03-28', '2026-04-02' ), [ '2026-03-28T02:30:00+01:00', '2026-03-30T02:30:00+02:00', '2026-03-31T02:30:00+02:00' ] );
  check ( padOccurrences ( 'daily', [ 'start' => '2026-10-24T02:30:00', 'timezone' => 'Europe/Amsterdam', 'count' => 3 ], '2026-10-24', '2026-10-28' ), [ '2026-10-24T02:30:00+02:00', '2026-10-25T02:30:00+02:00', '2026-10-26T02:30:00+01:00' ] );
  check ( padOccurrences ( 'weekly', [ 'start' => '2026-11-02', 'byday' => [ 'MO', 'WE' ], 'count' => 4, 'exclude' => [ '2026-11-04' ] ], '2026-11-03', '2026-12-01' ), [ '2026-11-09', '2026-11-11' ] );
  check ( padOccurrences ( 'rrule', [ 'start' => '1997-09-02T09:00:00', 'timezone' => 'America/New_York', 'rrule' => 'FREQ=DAILY;INTERVAL=2;COUNT=3' ], '1997-09-01', '1997-10-01' ), [ '1997-09-02T09:00:00-04:00', '1997-09-04T09:00:00-04:00', '1997-09-06T09:00:00-04:00' ] );
  check ( padOccurrences ( 'rrule', [ 'start' => '2026-01-31', 'rrule' => 'FREQ=MONTHLY;BYMONTHDAY=-1;UNTIL=20260331' ], '2026-01-01', '2026-05-01' ), [ '2026-01-31', '2026-02-28', '2026-03-31' ] );
  check ( padOccurrences ( 'daily', [ 'start' => '2026-11-01T12:00Z', 'count' => 1 ], '2026-11-01', '2026-11-02' ), [ '2026-11-01T12:00:00+00:00' ] );
  check ( padOccurrences ( 'daily', [ 'start' => '2026-10-25T02:30:00+01:00', 'timezone' => 'Europe/Amsterdam', 'count' => 1 ], '2026-10-25', '2026-10-26' ), [ '2026-10-25T02:30:00+01:00' ] );
  foreach ( [ [ 'daily', [ 'start' => '2026-01-01', 'limit' => 1 ] ], [ 'daily', [ 'start' => '2026-02-30' ] ], [ 'rrule', [ 'start' => '2026-01-01', 'rrule' => 'FREQ=MONTHLY;BYSETPOS=1' ] ], [ 'rrule', [ 'start' => '2026-01-01', 'rrule' => 'FREQ=DAILY;COUNT=2;UNTIL=20261231' ] ], [ 'weekly', [ 'start' => '2026-01-01', 'byday' => [ 'MO' ] ] ] ] as [ $kind, $rule ] ) {
    try { padOccurrences ( $kind, $rule, '2026-01-01', '2027-01-01' ); throw new Exception ( 'Expected rejection' ); } catch ( InvalidArgumentException | LengthException $e ) {}
  }

  check ( padOccurrences ( 'daily', [ 'start' => '2026-11-01T12:00+01:00', 'count' => 1 ], '2026-11-01', '2026-11-02' ), [ '2026-11-01T11:00:00+00:00' ] );
  check ( padOccurrences ( 'weekly', [ 'start' => '2026-11-02', 'byday' => [ 1, 1 ], 'weekstart' => 1, 'count' => 2 ], '2026-11-01', '2026-12-01' ), [ '2026-11-02', '2026-11-09' ] );
  check ( padOccurrences ( 'monthly', [ 'start' => '2026-01-01', 'monthday' => [ 1, 1, -1, -1 ], 'count' => 3 ], '2026-01-01', '2026-03-01' ), [ '2026-01-01', '2026-01-31', '2026-02-01' ] );
  check ( padOccurrences ( 'yearly', [ 'start' => '2026-01-01', 'month' => [ 1, 1, 2, 2 ], 'count' => 3 ], '2026-01-01', '2028-01-01' ), [ '2026-01-01', '2026-02-01', '2027-01-01' ] );
  foreach ( [ 'BYDAY=1', 'BYDAY=MO,2', 'BYDAY=+1MO', 'WKST=1', 'WKST=MO,TU' ] as $invalid ) {
    try { padOccurrences ( 'rrule', [ 'start' => '2026-11-02', 'rrule' => 'FREQ=WEEKLY;' . $invalid ], '2026-11-01', '2026-12-01' ); throw new Exception ( 'Expected RRULE weekday rejection' ); } catch ( InvalidArgumentException $e ) {}
  }
  foreach ( [ [ 'weekly', 'byday', 'MO' ], [ 'monthly', 'monthday', 1 ], [ 'yearly', 'month', 1 ] ] as [ $kind, $field, $value ] ) {
    try { padOccurrences ( $kind, [ 'start' => '2026-01-01', $field => array_fill ( 0, 257, $value ) ], '2026-01-01', '2027-01-01' ); throw new Exception ( 'Expected filter bound' ); } catch ( LengthException $e ) {}
  }
  $fraction = new DateTimeImmutable ( '2026-01-01T00:00:00.500000Z' );
  foreach ( [ [ [ 'start' => $fraction ], '2026-01-01', '2026-02-01' ], [ [ 'start' => '2026-01-01' ], $fraction, '2026-02-01' ], [ [ 'start' => '2026-01-01', 'exclude' => [ $fraction ] ], '2026-01-01', '2026-02-01' ] ] as [ $rule, $from, $to ] ) {
    try { padOccurrences ( 'daily', $rule, $from, $to ); throw new Exception ( 'Expected fractional-second rejection' ); } catch ( InvalidArgumentException $e ) {}
  }
  echo "Recurrences: month ends, leap days, DST gaps/folds, exclusions, RRULE, COUNT/UNTIL and limits passed\n";
?>
