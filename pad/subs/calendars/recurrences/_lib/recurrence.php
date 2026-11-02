<?php
  function padRecurrenceInteger ( $value, $min, $max, $name ) {
    if ( ! is_int ( $value ) and ( ! is_string ( $value ) or ! preg_match ( '/^-?\d+$/D', $value ) ) ) throw new InvalidArgumentException ( "Invalid recurrence $name" );
    if ( $value < $min or $value > $max ) throw new InvalidArgumentException ( "Invalid recurrence $name" );
    return (int) $value;
  }

  // A repeated wall time has two instants: select the first. A skipped wall time has none.
  function padRecurrenceWall ( $wall, $zone ) {
    $utc = DateTimeImmutable::createFromFormat ( '!Y-m-d H:i:s', $wall, new DateTimeZone ( 'UTC' ) );
    if ( ! $utc or $utc->format ( 'Y-m-d H:i:s' ) !== $wall ) return NULL;
    $stamp = $utc->getTimestamp (); $matches = [];
    $transitions = $zone->getTransitions ( $stamp - 172800, $stamp + 172800 );
    $offsets = $transitions === FALSE ? [ $zone->getOffset ( $utc ) ] : array_column ( $transitions, 'offset' );
    foreach ( array_unique ( $offsets ) as $offset ) {
      $candidate = ( new DateTimeImmutable ( '@' . ( $stamp - $offset ) ) )->setTimezone ( $zone );
      if ( $candidate->format ( 'Y-m-d H:i:s' ) === $wall ) $matches [$candidate->getTimestamp ()] = $candidate;
    }
    if ( ! $matches ) return NULL;
    ksort ( $matches, SORT_NUMERIC ); return reset ( $matches );
  }

  function padRecurrenceDate ( $value, $zone ) {
    if ( $value instanceof DateTimeInterface ) {
      if ( $value->format ( 'u' ) !== '000000' ) throw new InvalidArgumentException ( 'Recurrence dates require whole seconds' );
      return DateTimeImmutable::createFromInterface ( $value )->setTimezone ( $zone );
    }
    if ( ! is_string ( $value ) or ! preg_match ( '/^(\d{4}-\d{2}-\d{2})(?:[T ](\d{2}:\d{2})(?::(\d{2}))?(Z|[+-]\d{2}:\d{2})?)?$/D', $value, $m ) ) throw new InvalidArgumentException ( 'Recurrence dates must be explicit ISO dates or date-times' );
    $wall = $m [1] . ' ' . ( $m [2] ?? '00:00' ) . ':' . ( ( $m [3] ?? '' ) !== '' ? $m [3] : '00' );
    $offset = $m [4] ?? '';
    if ( $offset !== '' and $offset !== 'Z' and ( (int) substr ( $offset, 4, 2 ) > 59 or (int) substr ( $offset, 1, 2 ) * 60 + (int) substr ( $offset, 4, 2 ) > 840 ) ) throw new InvalidArgumentException ( 'Invalid UTC offset' );
    $date = padRecurrenceWall ( $wall, $offset === '' ? $zone : new DateTimeZone ( $offset === 'Z' ? 'UTC' : $offset ) );
    if ( ! $date ) throw new InvalidArgumentException ( 'Invalid or nonexistent recurrence date-time' );
    return $date->setTimezone ( $zone );
  }

  function padRecurrenceList ( $values ) {
    if ( is_int ( $values ) or is_string ( $values ) ) $values = [ $values ];
    if ( ! is_array ( $values ) or ! array_is_list ( $values ) or ! $values ) throw new InvalidArgumentException ( 'Recurrence filters need nonempty lists' );
    if ( count ( $values ) > 256 ) throw new LengthException ( 'Recurrence filter exceeds 256 entries' );
    return $values;
  }

  function padRecurrenceDays ( $values ) {
    $names = [ 'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6, 'SU' => 7 ];
    $out = [];
    foreach ( padRecurrenceList ( $values ) as $value ) $out [] = is_string ( $value ) && isset ( $names [$value] ) ? $names [$value] : padRecurrenceInteger ( $value, 1, 7, 'weekday' );
    if ( ! $out ) throw new InvalidArgumentException ( 'Empty recurrence weekdays' );
    return array_unique ( $out );
  }

  // Monthly weekday selection; yearly rules use this selection within explicit BYMONTHs.
  function padRecurrenceMonthSelection ( $day, $byday, $positions ) {
    $names = [ 'MO'=>1,'TU'=>2,'WE'=>3,'TH'=>4,'FR'=>5,'SA'=>6,'SU'=>7 ]; $selected=[];
    $first=$day->modify('first day of this month'); $length=(int)$day->format('t');
    foreach(padRecurrenceList($byday) as $token){
      if(!is_string($token)||!preg_match('/^([+-]?[1-5])?(MO|TU|WE|TH|FR|SA|SU)$/D',$token,$m))throw new InvalidArgumentException('Monthly BYDAY needs weekday names with optional ordinal 1 to 5 or -1 to -5');
      $days=[];for($n=1;$n<=$length;$n++)if((int)$first->modify('+'.($n-1).' days')->format('N')===$names[$m[2]])$days[]=$n;
      if($m[1]!==''){$ordinal=(int)$m[1];$at=$ordinal>0?$ordinal-1:count($days)+$ordinal;if(isset($days[$at]))$selected[$days[$at]]=TRUE;}
      else foreach($days as $n)$selected[$n]=TRUE;
    }
    $days=array_keys($selected);sort($days);
    if($positions!==NULL){$selected=[];foreach(padRecurrenceList($positions) as $p){$p=padRecurrenceInteger($p,-31,31,'BYSETPOS');if(!$p)throw new InvalidArgumentException('BYSETPOS cannot be zero');$at=$p>0?$p-1:count($days)+$p;if(isset($days[$at]))$selected[$days[$at]]=TRUE;}}
    return isset($selected[(int)$day->format('j')]);
  }

  function padRecurrenceExpand ( $kind, $type, $rule, $from, $to ) {
    $allowed = array_merge ( [ 'start', 'timezone', 'interval', 'count', 'until', 'exclude', 'limit' ], $type ['fields'] );
    if ( array_diff ( array_keys ( $rule ), $allowed ) ) throw new InvalidArgumentException ( 'Unsupported recurrence option' );
    $tz = $rule ['timezone'] ?? 'UTC';
    if ( ! is_string ( $tz ) or ! in_array ( $tz, DateTimeZone::listIdentifiers ( DateTimeZone::ALL_WITH_BC ), TRUE ) ) throw new InvalidArgumentException ( 'Recurrence needs an IANA timezone' );
    $zone = new DateTimeZone ( $tz );
    $start = padRecurrenceDate ( $rule ['start'] ?? NULL, $zone );
    $from = padRecurrenceDate ( $from, $zone ); $to = padRecurrenceDate ( $to, $zone );
    if ( $from >= $to ) throw new InvalidArgumentException ( 'Recurrence bounds must increase' );
    $dateOnly = is_string ( $rule ['start'] ) && strlen ( $rule ['start'] ) === 10;
    $interval = padRecurrenceInteger ( $rule ['interval'] ?? 1, 1, 10000, 'interval' );
    $limit = padRecurrenceInteger ( $rule ['limit'] ?? 1000, 1, 10000, 'limit' );
    $count = isset ( $rule ['count'] ) ? padRecurrenceInteger ( $rule ['count'], 1, 1000000, 'count' ) : PHP_INT_MAX;
    if ( isset ( $rule ['count'], $rule ['until'] ) ) throw new InvalidArgumentException ( 'COUNT and UNTIL are mutually exclusive' );
    $until = isset ( $rule ['until'] ) ? padRecurrenceDate ( $rule ['until'], $zone ) : NULL;
    if ( $until and $until < $start ) throw new InvalidArgumentException ( 'UNTIL precedes DTSTART' );
    $monthWeekdays = in_array($kind,['monthly','yearly'],TRUE) && isset($rule['byday']);
    if(isset($rule['setpos'])&&!$monthWeekdays)throw new InvalidArgumentException('BYSETPOS requires monthly/yearly BYDAY');
    if($monthWeekdays&&isset($rule['monthday']))throw new InvalidArgumentException('Combining BYDAY and BYMONTHDAY is not supported');
    if($kind==='yearly'&&$monthWeekdays&&!isset($rule['month']))throw new InvalidArgumentException('Yearly BYDAY requires explicit BYMONTH');
    $weekdays = padRecurrenceDays ( $monthWeekdays ? [1] : ($rule ['byday'] ?? [ (int) $start->format ( 'N' ) ]) );
    $weekend=$kind==='businessday' ? padRecurrenceDays($rule['weekend']??['SA','SU']) : [];
    $holidays=[];
    if($kind==='businessday'&&isset($rule['holidays'])&&(!is_array($rule['holidays'])||!array_is_list($rule['holidays'])||count($rule['holidays'])>10000))throw new InvalidArgumentException('Business holidays need a list of at most 10000 dates');
    if($kind==='businessday')foreach($rule['holidays']??[] as $holiday){if(!is_string($holiday)||strlen($holiday)!==10)throw new InvalidArgumentException('Business holidays must be ISO dates');$holidays[padRecurrenceDate($holiday,$zone)->format('Y-m-d')]=TRUE;}
    $weekstart = padRecurrenceDays ( [ $rule ['weekstart'] ?? 'MO' ] ) [0];
    $monthdays = [];
    foreach ( padRecurrenceList ( $rule ['monthday'] ?? [ (int) $start->format ( 'j' ) ] ) as $v ) {
      $v = padRecurrenceInteger ( $v, -31, 31, 'monthday' );
      if ( ! $v ) throw new InvalidArgumentException ( 'Month day cannot be zero' );
      $monthdays [] = $v;
    }
    $months = [];
    foreach ( padRecurrenceList ( $rule ['month'] ?? [ (int) $start->format ( 'n' ) ] ) as $v ) $months [] = padRecurrenceInteger ( $v, 1, 12, 'month' );
    $months = array_values ( array_unique ( $months, SORT_NUMERIC ) );
    $monthdays = array_values ( array_unique ( $monthdays, SORT_NUMERIC ) );
    if($kind==='yearly'&&isset($rule['setpos'])&&count($months)!==1)throw new InvalidArgumentException('Yearly BYSETPOS requires exactly one BYMONTH');
    if ( ! $months or ! $monthdays ) throw new InvalidArgumentException ( 'Empty recurrence month list' );
    $excluded = [];
    if ( isset ( $rule ['exclude'] ) and ! is_array ( $rule ['exclude'] ) ) throw new InvalidArgumentException ( 'Exclusions must be a list' );
    if ( count ( $rule ['exclude'] ?? [] ) > 10000 ) throw new LengthException ( 'Too many exclusions' );
    foreach ( $rule ['exclude'] ?? [] as $v ) {
      $ex = padRecurrenceDate ( $v, $zone );
      $excluded [ is_string ( $v ) && strlen ( $v ) === 10 ? $ex->format ( 'Y-m-d' ) : (string) $ex->getTimestamp () ] = TRUE;
    }
    $utc = new DateTimeZone ( 'UTC' );
    $day = new DateTimeImmutable ( $start->format ( 'Y-m-d' ), $utc );
    $last = new DateTimeImmutable ( $to->format ( 'Y-m-d' ), $utc );
    if ( $until ) $last = min ( $last, new DateTimeImmutable ( $until->format ( 'Y-m-d' ), $utc ) );
    if ( $last < $day ) return [];
    $span = (int) $day->diff ( $last )->days;
    if ( $span > 200000 ) throw new LengthException ( 'Recurrence scan exceeds 200000 days from DTSTART' );
    $origin = $day; $weekOffset = ( (int) $day->format ( 'N' ) - $weekstart + 7 ) % 7;
    $out = []; $seen = 0; $businessSeen=0;
    for ( $d = 0; $d <= $span; $d++, $day = $day->modify ( '+1 day' ) ) {
      $monthDelta = ( (int) $day->format ( 'Y' ) - (int) $origin->format ( 'Y' ) ) * 12 + (int) $day->format ( 'n' ) - (int) $origin->format ( 'n' );
      $monthDay = in_array ( (int) $day->format ( 'j' ), $monthdays, TRUE ) || in_array ( (int) $day->format ( 'j' ) - (int) $day->format ( 't' ) - 1, $monthdays, TRUE );
      if($monthWeekdays)$monthDay=padRecurrenceMonthSelection($day,$rule['byday'],$rule['setpos']??NULL);
      $businessStamp=$kind==='businessday'?padRecurrenceWall($day->format('Y-m-d').' '.$start->format('H:i:s'),$zone):NULL;
      $business=$kind==='businessday'&&$businessStamp&&!isset($excluded[(string)$businessStamp->getTimestamp()])&&!in_array((int)$day->format('N'),$weekend,TRUE)&&!isset($holidays[$day->format('Y-m-d')])&&!isset($excluded[$day->format('Y-m-d')]);
      $matches = match ( $kind ) {
        'businessday' => $business && $businessSeen++ % $interval === 0,
        'daily' => $d % $interval === 0,
        'weekly' => intdiv ( $d + $weekOffset, 7 ) % $interval === 0 and in_array ( (int) $day->format ( 'N' ), $weekdays, TRUE ),
        'monthly' => $monthDelta % $interval === 0 and $monthDay,
        'yearly' => ( (int) $day->format ( 'Y' ) - (int) $origin->format ( 'Y' ) ) % $interval === 0 and in_array ( (int) $day->format ( 'n' ), $months, TRUE ) and $monthDay,
      };
      if ( $d === 0 and ! $matches and $kind !== 'businessday' ) throw new InvalidArgumentException ( 'DTSTART must match recurrence filters' );
      if ( ! $matches ) continue;
      $occurrence = $d === 0 ? $start : padRecurrenceWall ( $day->format ( 'Y-m-d' ) . ' ' . $start->format ( 'H:i:s' ), $zone );
      if ( ! $occurrence ) continue;
      if ( $until and $occurrence > $until or $occurrence >= $to ) break;
      if ( ++$seen > $count ) break;
      if ( $occurrence < $from or isset ( $excluded [$occurrence->format ( 'Y-m-d' )] ) or isset ( $excluded [(string) $occurrence->getTimestamp ()] ) ) continue;
      if ( count ( $out ) >= $limit ) throw new LengthException ( 'Recurrence output exceeds limit; narrow the window' );
      $out [] = $occurrence->format ( $dateOnly ? 'Y-m-d' : 'Y-m-d\TH:i:sP' );
    }
    return $out;
  }

  function padRecurrenceRrule ( $rule ) {
    $text = $rule ['rrule'] ?? NULL; unset ( $rule ['rrule'] );
    if ( ! is_string ( $text ) or strlen ( $text ) > 2048 ) throw new InvalidArgumentException ( 'RRULE must be a bounded string' );
    $parts = [];
    foreach ( explode ( ';', preg_replace ( '/^RRULE:/', '', strtoupper ( $text ) ) ) as $part ) {
      if ( ! preg_match ( '/^([A-Z]+)=([^;]+)$/D', $part, $m ) or isset ( $parts [$m [1]] ) ) throw new InvalidArgumentException ( 'Invalid or duplicate RRULE part' );
      $parts [$m [1]] = $m [2];
    }
    if ( array_diff ( array_keys ( $parts ), [ 'FREQ', 'INTERVAL', 'COUNT', 'UNTIL', 'BYDAY', 'BYMONTHDAY', 'BYMONTH', 'BYSETPOS', 'WKST' ] ) ) throw new InvalidArgumentException ( 'Unsupported RRULE part' );
    if ( isset ( $parts ['BYDAY'] ) and ! preg_match ( '/^(?:[+-]?[1-5])?(?:MO|TU|WE|TH|FR|SA|SU)(?:,(?:[+-]?[1-5])?(?:MO|TU|WE|TH|FR|SA|SU))*$/D', $parts ['BYDAY'] ) ) throw new InvalidArgumentException ( 'RRULE BYDAY requires weekday names' );
    if ( isset ( $parts ['WKST'] ) and ! preg_match ( '/^(?:MO|TU|WE|TH|FR|SA|SU)$/D', $parts ['WKST'] ) ) throw new InvalidArgumentException ( 'RRULE WKST requires a weekday name' );
    $kind = strtolower ( $parts ['FREQ'] ?? '' );
    if ( ! in_array ( $kind, [ 'daily', 'weekly', 'monthly', 'yearly' ], TRUE ) ) throw new InvalidArgumentException ( 'Unsupported RRULE frequency' );
    foreach ( [ 'INTERVAL' => 'interval', 'COUNT' => 'count', 'BYDAY' => 'byday', 'BYMONTHDAY' => 'monthday', 'BYMONTH' => 'month', 'BYSETPOS' => 'setpos', 'WKST' => 'weekstart' ] as $key => $name )
      if ( isset ( $parts [$key] ) ) {
        if ( array_key_exists ( $name, $rule ) ) throw new InvalidArgumentException ( 'Conflicting recurrence options' );
        $rule [$name] = str_starts_with ( $key, 'BY' ) ? explode ( ',', $parts [$key] ) : $parts [$key];
      }
    if ( isset ( $parts ['UNTIL'] ) ) {
      if ( isset ( $rule ['until'] ) ) throw new InvalidArgumentException ( 'Conflicting UNTIL' );
      $dateOnly = is_string ( $rule ['start'] ?? NULL ) && strlen ( $rule ['start'] ) === 10;
      if ( ! preg_match ( $dateOnly ? '/^(\d{4})(\d{2})(\d{2})$/D' : '/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z$/D', $parts ['UNTIL'], $m ) ) throw new InvalidArgumentException ( 'UNTIL must match DTSTART date type; timed UNTIL must be UTC' );
      $rule ['until'] = "$m[1]-$m[2]-$m[3]" . ( $dateOnly ? '' : "T$m[4]:$m[5]:$m[6]Z" );
    }
    return [ $kind, $rule ];
  }
?>
