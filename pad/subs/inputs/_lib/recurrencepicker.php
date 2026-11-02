<?php
include_once PAD.'lib/recurrence.php';
function padInputRecurrence($value){
 if(!is_array($value)||array_diff(array_keys($value),['kind','start','timezone','interval','count','byday','exclude']))throw new InvalidArgumentException('Unsupported recurrence fields');
 $kind=$value['kind']??'daily';if(!in_array($kind,['daily','weekly','monthly','yearly'],TRUE))throw new InvalidArgumentException('Unsupported recurrence frequency');
 foreach(['start','timezone','interval','count','byday','exclude'] as $k)if(isset($value[$k])&&!is_scalar($value[$k]))throw new InvalidArgumentException('Recurrence fields must be text');
 $interval=filter_var($value['interval']??1,FILTER_VALIDATE_INT);$count=filter_var($value['count']??10,FILTER_VALIDATE_INT);if($interval===FALSE||$interval<1||$interval>100||$count===FALSE||$count<1||$count>100)throw new InvalidArgumentException('Recurrence interval/count must be 1..100');
 $rule=['start'=>(string)($value['start']??''),'timezone'=>(string)($value['timezone']??'UTC'),'interval'=>$interval,'count'=>$count];
 $days=trim((string)($value['byday']??''));if($days!==''){if($kind!=='weekly')throw new InvalidArgumentException('Weekdays only apply to weekly recurrence');$rule['byday']=array_map('trim',explode(',',$days));}
 $exclude=trim((string)($value['exclude']??''));if(strlen($exclude)>10000)throw new LengthException('Too many exclusions');if($exclude!=='')$rule['exclude']=preg_split('/[\r\n,]+/',$exclude);
 if(!in_array($rule['timezone'],DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE))throw new InvalidArgumentException('Recurrence timezone must be IANA');
 // The authoritative engine validates exact civil time, unsupported combinations and exclusions.
 $zone=new DateTimeZone($rule['timezone']);try{$from=new DateTimeImmutable($rule['start'],$zone);}catch(Exception $e){throw new InvalidArgumentException('Invalid recurrence start');}$to=$from->modify('+1 year');$occurrences=padOccurrences($kind,$rule,$from->format('Y-m-d'),$to->format('Y-m-d'));
 return ['kind'=>$kind,'rule'=>$rule,'occurrences'=>$occurrences];
}
