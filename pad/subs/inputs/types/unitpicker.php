<?php

  // unitpicker - A numeric value paired with a unit from a physical measurement family.
  //

include_once PAD . 'subs/units/_lib/units.php';
$family=(string)padInputTake('family','length');
$definition=padUnits($family);
if (!is_array($definition) || !isset($definition['units'])) return '';
$units=array_keys($definition['units']);
$given=padInputTake('value',[]); if(!is_array($given))$given=['value'=>$given];
$unit=(string)padInputTake('unit',$given['unit']??$definition['canonical']);
$value=padFormValue(padInputName().'[value]',(string)($given['value']??''));
$unit=padFormValue(padInputName().'[unit]',$unit);
if(!in_array($unit,$units,TRUE))$units[]=$unit; // Retain an invalid posted value for correction.
return padInputField(function($head,$rest,$label,$error,$id,$worded) use($units,$unit,$value){
 $name=padInputEscape(padInputName());$id=padInputEscape($id);
 $head=preg_replace('/name="[^"]*"/','name="'.$name.'[value]"',$head);
 $disabled=isset($GLOBALS['padInputNow']['attrs']['disabled'])?' disabled':'';
 $select='<label for="'.$id.'-unit">Unit</label><select id="'.$id.'-unit" name="'.$name.'[unit]"'.$disabled.'>';
 foreach($units as $one)$select.='<option value="'.padInputEscape($one).'"'.($one===$unit?' selected':'').'>'.padInputEscape($one).'</option>';
 $own=padInputOwn($rest,['step'=>'any']);
 return padInputFrame('unitpicker',$worded,$label.'<div class="pi-unit"><input type="number" '.$head.$own.' value="'.padInputEscape($value).'"'.$rest.'>'.$select.'</select></div>',$error);
});
?>
