<?php
  // Calendar occurrences only: the existing cron scheduler remains independent.
  function padOccurrences ( $kind, $rule, $from, $to ) {
    include_once PAD . 'subs/calendars/recurrences/_lib/recurrence.php';
    if ( ! padKind ( 'calendars/recurrences', $kind ) or ! is_array ( $rule ) ) throw new InvalidArgumentException ( 'Unknown recurrence or invalid rule' );
    if ( $kind === 'rrule' ) [ $kind, $rule ] = padRecurrenceRrule ( $rule );
    $type = include PAD . "subs/calendars/recurrences/types/$kind.php";
    return padRecurrenceExpand ( $kind, $type, $rule, $from, $to );
  }
?>
