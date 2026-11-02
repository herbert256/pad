<?php
// One metric; delta is caller-supplied numeric change, never inferred as a trend or forecast.
$e=fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
$value=padWidgetText('value',is_scalar($padParm)?(string)$padParm:'');$label=padWidgetText('label','Value');$detail=padWidgetText('detail');$unit=padWidgetText('unit');$baseline=padWidgetText('baseline');$delta=padTagParm('delta',NULL);
if($delta!==NULL&&(!is_numeric($delta)||!is_finite((float)$delta))){padError('Stat delta must be a finite number');$delta=NULL;}
return padWidgetSheet(padWidgetFile('widgets/stat.css')).'<dl class="pad-stat"><dt>'.$e($label).'</dt><dd class="pad-stat-value">'.$e($value).($unit!==''?' '.$e($unit):'').'</dd>'.($baseline!==''?'<dd>Compared with: '.$e($baseline).'</dd>':'').($delta!==NULL?'<dd class="pad-stat-delta">Change: '.($delta>0?'+':'').$e($delta).'</dd>':'').($detail!==''?'<dd>'.$e($detail).'</dd>':'').'</dl>';
?>
