<?php
function padTableRanked($rows,$score='score',$method='competition',$descending=TRUE,$id='id',$previous='previous'){
 if(!is_array($rows)||count($rows)>10000||!in_array($method,['dense','competition','ordinal'],TRUE))throw new InvalidArgumentException('ranked supports 10000 rows and dense/competition/ordinal methods');$seen=[];
 foreach($rows as $r){if(!is_array($r)||!is_scalar($r[$id]??NULL)||(string)$r[$id]===''||isset($seen[(string)$r[$id]])||!is_numeric($r[$score]??NULL)||!is_finite((float)$r[$score]))throw new InvalidArgumentException('ranked requires unique IDs and finite scores');$seen[(string)$r[$id]]=TRUE;}
 usort($rows,fn($a,$b)=>(($a[$score]<=>$b[$score])*($descending?-1:1))?:strcmp((string)$a[$id],(string)$b[$id]));$last=NULL;$rank=0;$dense=0;
 foreach($rows as $i=>&$r){$v=$r[$score];if($i===0||($v<=>$last)!==0){$rank=$i+1;$dense++;}$r['_rank']=$method==='dense'?$dense:($method==='ordinal'?$i+1:$rank);$p=$r[$previous]??NULL;if($p!==NULL&&(filter_var($p,FILTER_VALIDATE_INT)===FALSE||$p<1))throw new InvalidArgumentException('previous rank must be a positive integer');$r['_change']=$p===NULL?NULL:(int)$p-$r['_rank'];$last=$v;}unset($r);return $rows;
}
