<?php

  // dateformat - Strict formats: `Y-m-d`, `d/m/Y`, `m/d/Y`, `Y-m-dTH:i`. Leading zeroes and four-digit years 0001..9999 are mandatory. Gregorian round trips reject overflow; datetime-local values allow hours 00..23 and minutes 00..59. No timezone inference or suffixes. Empty values remain optional unless paired with required. PHP/browser share committed fixtures.
  //
  // ## Example
  //
  // ```pad
  // {input:form 'dateformat'}
  // {input:field type='text', 'date', label='Date', rules='dateformat:Y-m-d'/}
  // <button type='submit'>Validate</button>
  // {/input:form}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/rule-dateformat.php`.

return ['argument'=>fn($arg)=>in_array($arg,['Y-m-d','d/m/Y','m/d/Y','Y-m-dTH:i'],TRUE)?'':'use Y-m-d, d/m/Y, m/d/Y or Y-m-dTH:i',
 'check'=>function($value,$arg){$formats=['Y-m-d'=>'Y-m-d','d/m/Y'=>'d/m/Y','m/d/Y'=>'m/d/Y','Y-m-dTH:i'=>'Y-m-d\TH:i'];if(!isset($formats[$arg])||!is_string($value))return FALSE;$d=DateTimeImmutable::createFromFormat('!'.$formats[$arg],$value,new DateTimeZone('UTC'));return $d&&$d->format($formats[$arg])===$value&&$d->format('Y')!=='0000';},
 'message'=>':label must match date format :n'];
