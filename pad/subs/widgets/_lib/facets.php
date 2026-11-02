<?php
include_once PAD.'subs/search/_lib/facets.php';
function padFacetDecode($posted){
 if(!is_array($posted)||count($posted)>50)throw new InvalidArgumentException('facet request must be a field map of at most 50 fields');$out=[];$count=0;
 foreach($posted as $field=>$tokens){if(!preg_match('/^[A-Za-z_][A-Za-z0-9_.-]*$/D',(string)$field)||!is_array($tokens))throw new InvalidArgumentException('invalid facet field');foreach($tokens as $token){if(++$count>1000||!is_string($token)||strlen($token)>2000)throw new InvalidArgumentException('facet token limit');$v=json_decode($token,TRUE,4,JSON_THROW_ON_ERROR);if(!is_scalar($v)&&$v!==NULL)throw new InvalidArgumentException('facet value must be scalar');if(!in_array($v,$out[$field]??[],TRUE))$out[$field][]=$v;}}
 return $out;
}
function padWidgetFacets($info,$selected=[],$name='facet'){
 if(!is_array($info)||!is_array($info['facets']??NULL)||padSearchFilterError($selected)!==''||!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D',$name))throw new InvalidArgumentException('facets requires search metadata and typed selections');
 $esc=fn($s)=>htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$facets=$info['facets'];foreach($selected as $field=>$values){$facets[$field]??=[];foreach((is_array($values)?$values:[$values]) as $value)if(!in_array($value,array_column($facets[$field],'value'),TRUE))$facets[$field][]=['value'=>$value,'count'=>0];}
 $html='<div class="pad-facets">';$n=0;
 foreach($facets as $field=>$buckets){if(!preg_match('/^[A-Za-z_][A-Za-z0-9_.-]*$/D',(string)$field))throw new InvalidArgumentException('invalid facet name');$html.='<fieldset><legend>'.$esc($field).'</legend>';
 foreach($buckets as $bucket){if(++$n>1000)throw new LengthException('facets supports at most 1000 buckets');$v=$bucket['value'];$count=$bucket['count'];if((!is_scalar($v)&&$v!==NULL)||!is_int($count)||$count<0)throw new InvalidArgumentException('invalid facet bucket');$checked=in_array($v,is_array($selected[$field]??[])?($selected[$field]??[]):[$selected[$field]],TRUE);$token=json_encode($v,JSON_PRESERVE_ZERO_FRACTION|JSON_THROW_ON_ERROR);$label=$v===NULL?'null':(is_bool($v)?($v?'true':'false'):(string)$v);
 $html.='<label><input type="checkbox" name="'.$esc($name.'['.$field.'][]').'" value="'.$esc($token).'"'.($checked?' checked':'').'> '.$esc($label).' ('.$count.')</label> ';}$html.='</fieldset>';}
 return $html.($n?'':'<p>No facets.</p>').'</div>';
}
