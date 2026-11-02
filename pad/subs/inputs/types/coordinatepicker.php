<?php
// Posts name[latitude], name[longitude]; decimal degrees in WGS84, no location request.
$given=padInputTake('value',[]);if(!is_array($given))$given=[];
$latitude=padFormValue(padInputName().'[latitude]',is_scalar($given['latitude']??NULL)?(string)$given['latitude']:'');
$longitude=padFormValue(padInputName().'[longitude]',is_scalar($given['longitude']??NULL)?(string)$given['longitude']:'');
return padInputField(function($head,$rest,$label,$error,$id,$worded)use($latitude,$longitude){
 $name=padInputName();$safe=padInputEscape($id);
 // A shared accessible name belongs to the group. Each native input keeps the
 // distinct name from its own visible label rather than inheriting 'Coordinates'.
 $groupNames='';foreach(['aria-label','aria-labelledby'] as $attribute)
  if(preg_match('/\s'. $attribute .'="([^"]*)"/',$rest,$match)){$groupNames.=$match[0];$rest=str_replace($match[0],'',$rest);}
 $legend=$worded!==''?$worded:padValidateName($name);
 $html='<fieldset class="pi-coordinates" id="'.$safe.'"'.$groupNames.'><legend>'.padInputEscape($legend).'</legend>';
 foreach(['latitude'=>[$latitude,-90,90,'Latitude'],'longitude'=>[$longitude,-180,180,'Longitude']] as $axis=>[$value,$min,$max,$axisLabel]){
  $childName=$name.'['.$axis.']';$childId=$id.'-'.$axis;$childRest=$rest;
  $message=padFormError($childName,$axisLabel);$childError='';
  if($message!==''){
   $described='';if(preg_match('/\saria-describedby="([^"]*)"/',$childRest,$match))$described=html_entity_decode($match[1],ENT_QUOTES,'UTF-8').' ';
   $childRest=preg_replace('/\saria-(?:invalid|describedby)="[^"]*"/','',$childRest);
   $childRest.=' aria-invalid="true" aria-describedby="'.padInputEscape($described.$childId.'-error').'"';
   $childError='<span class="error" id="'.padInputEscape($childId.'-error').'">'.padInputEscape($message).'</span>';
  }
  $own=padInputOwn($childRest,['min'=>(string)$min,'max'=>(string)$max,'step'=>'any']);
  $html.='<div><label for="'.padInputEscape($childId).'">'.$axisLabel.'</label><input type="number" name="'.padInputEscape($childName).'" id="'.padInputEscape($childId).'"'.$own.' value="'.padInputEscape($value).'"'.$childRest.'>'.$childError.'</div>';
 }
 return padInputFrame('coordinatepicker',$worded,$html.'</fieldset>',$error);
});
?>
