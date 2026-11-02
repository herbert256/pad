<?php
include_once PAD.'subs/diffs/_lib/compare.php';
function padDiffSequence($before,$after,$options=[]){
 if(array_diff(array_keys($options),['key']))throw new InvalidArgumentException('sequence supports only key');$key=$options['key']??NULL;if($key!==NULL&&(!is_string($key)||$key===''))throw new InvalidArgumentException('key must be a nonempty field name');$maps=[];
 foreach([$before,$after] as $rows){if(!is_array($rows)||!array_is_list($rows)||count($rows)>10000)throw new InvalidArgumentException('sequence requires lists of at most 10000 unique IDs or records');$nodes=0;padDiffValidate($rows,$nodes);$map=[];foreach($rows as $at=>$row){$id=$key===NULL?$row:(is_array($row)?($row[$key]??NULL):NULL);if((!is_int($id)&&!is_string($id))||$id==='')throw new InvalidArgumentException('identity must be a nonempty string or integer');$token=serialize($id);if(isset($map[$token]))throw new InvalidArgumentException('duplicate sequence identity');$map[$token]=['at'=>$at,'id'=>$id,'value'=>$row];}$maps[]=$map;}
 [$a,$b]=$maps;$tokens=[];$positions=[];foreach($b as $token=>$entry)if(isset($a[$token])){$tokens[]=$token;$positions[]=$a[$token]['at'];}
 // Longest increasing common subsequence keeps the maximal number of unchanged identities.
 $tails=[];$tailAt=[];$prev=[];foreach($positions as $i=>$pos){$lo=0;$hi=count($tails);while($lo<$hi){$mid=intdiv($lo+$hi,2);if($tails[$mid]<$pos)$lo=$mid+1;else $hi=$mid;}$prev[$i]=$lo?$tailAt[$lo-1]:NULL;$tails[$lo]=$pos;$tailAt[$lo]=$i;}$keep=[];if($tailAt)for($i=end($tailAt);$i!==NULL;$i=$prev[$i])$keep[$tokens[$i]]=TRUE;
 $out=[];foreach($a as $token=>$entry)if(!isset($b[$token]))$out[]=['op'=>'remove','path'=>'/'.$entry['at'],'key'=>$entry['id'],'before'=>$entry['value']];
 foreach($b as $token=>$entry){$path='/'.$entry['at'];if(!isset($a[$token])){$out[]=['op'=>'add','path'=>$path,'key'=>$entry['id'],'after'=>$entry['value']];continue;}if(!isset($keep[$token]))$out[]=['op'=>'move','from'=>'/'.$a[$token]['at'],'path'=>$path,'key'=>$entry['id'],'before'=>$a[$token]['id'],'after'=>$entry['id']];if($key!==NULL){$changes=[];padDiffWalk((object)$a[$token]['value'],(object)$entry['value'],$path,$changes);foreach($changes as $change)$out[]=$change+['key'=>$entry['id']];}}
 return $out;
}

function padDiffSequenceHtml($a,$b,$options){$key=$options['key']??NULL;if($key===FALSE||$key==='')$key=NULL;$records=fn($v)=>is_array($v)?array_map(fn($row)=>$row instanceof stdClass?(array)$row:$row,$v):$v;$changes=padDiffSequence($records(padDiffValue($a)),$records(padDiffValue($b)),['key'=>$key]);foreach($changes as &$change)if($change['op']==='move')$change['path']='move '.$change['from'].' → '.$change['path'];unset($change);return padDiffRecordsHtml($changes,FALSE,$options['from'],$options['to']);}
