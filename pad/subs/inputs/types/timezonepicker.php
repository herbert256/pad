<?php
// Native IANA-zone selector, works without JS and retains invalid posted values for correction.
$reference=padInputTake('reference','2000-01-01T00:00:00Z');
if(!is_string($reference)||!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2})?(?:Z|[+-]\d{2}:\d{2})$/D',$reference)){$reference='2000-01-01T00:00:00Z';padError('Timezone reference needs an ISO instant with an explicit offset');}
try{$instant=new DateTimeImmutable($reference);$problem=DateTimeImmutable::getLastErrors();if($problem&&($problem['warning_count']||$problem['error_count']))throw new InvalidArgumentException('Invalid timezone date');}catch(Exception $e){$instant=new DateTimeImmutable('2000-01-01T00:00:00Z');padError('Invalid timezone reference');}
$zones=DateTimeZone::listIdentifiers();$zones[]='UTC';$zones=array_unique($zones);sort($zones);
$padInputNow['value']=padInputValue('UTC');$value=(string)$padInputNow['value'];
if(!in_array($value,$zones,TRUE))array_unshift($zones,$value);
return padInputField(function($head,$rest,$label,$error,$id,$worded)use($zones,$value,$instant){
 $html=$label.'<select '.$head.$rest.'>';foreach($zones as $zone){$known=in_array($zone,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),TRUE);$offset=$known?$instant->setTimezone(new DateTimeZone($zone))->format('P'):'';$html.='<option value="'.padInputEscape($zone).'"'.($zone===$value?' selected':'').'>'.padInputEscape(str_replace('_',' ',$zone).($offset!==''?' (UTC'.$offset.')':' (invalid zone)')).'</option>';}
 return padInputFrame('timezonepicker',$worded,$html.'</select><small>Offsets at '.padInputEscape($instant->format('Y-m-d H:i T')).'</small>',$error);
});
?>
