<?php
include_once PAD.'subs/widgets/_lib/jsonview.php';
function padWidgetInspector($record,$fields){
 if(!is_array($record)||!is_array($fields)||count($fields)>100)throw new InvalidArgumentException('inspector expects a record and at most 100 explicit fields');
 $esc=fn($s)=>htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$html='<dl class="pad-inspector">';
 foreach($fields as $key=>$meta){if(is_string($meta))$meta=['label'=>$meta];if(!is_string($key)||!is_array($meta))throw new InvalidArgumentException('inspector metadata must name fields');$html.='<dt>'.$esc($meta['label']??$key).'</dt><dd>';
 if(!array_key_exists($key,$record))$html.='<span>Absent</span>';
 elseif($record[$key]===NULL)$html.='<span>Null</span>';
 elseif(is_array($record[$key]))$html.=padJsonviewHtml($record[$key],1,(string)($meta['label']??$key));
 elseif(is_scalar($record[$key])){$value=is_bool($record[$key])?($record[$key]?'true':'false'):(string)$record[$key];$html.=($value===''?'Empty':$esc($value)).(isset($meta['unit'])?' '.$esc($meta['unit']):'');}
 else $html.='<span>Unsupported value</span>';
 $url=$meta['action']??'';if(is_string($url)&&preg_match('~^(?:https?://[^\s]+|/(?!/)[^\s]*|\?[^\s]*|\#[^\s]*)$~D',$url))$html.=' <a href="'.$esc($url).'">'.$esc($meta['actionlabel']??'Open').'</a>';$html.='</dd>';}
 return $html.'</dl>';
}
