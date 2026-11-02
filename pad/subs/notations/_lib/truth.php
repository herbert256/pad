<?php
include_once PAD.'subs/notations/_lib/karnaugh.php';
function padTruthRead($text) {
 if(strlen($text)>8000)throw new InvalidArgumentException('Truth expressions exceed 8000 bytes');
 $columns=[];$variables=[];
 foreach(preg_split('/[;\r\n]+/',trim($text)) as $line){
  if(trim($line)==='')continue;
  if(count($columns)>=8)throw new InvalidArgumentException('Truth table supports at most eight expression columns');
  if(!preg_match('/^([A-Za-z][A-Za-z0-9_]*)\s*=\s*(.+)$/',trim($line),$m))throw new InvalidArgumentException('Use named columns such as F = A and B');
  if(isset($columns[$m[1]])||isset($variables[$m[1]]))throw new InvalidArgumentException('Duplicate or previously used column name');
  $tree=padKarnaughParse($m[2]);if(is_string($tree))throw new InvalidArgumentException($tree);
  if(isset(padKarnaughUsed($tree)[$m[1]]))throw new InvalidArgumentException('A truth column cannot reference itself');
  foreach(padKarnaughUsed($tree) as $name=>$unused)if(!isset($columns[$name]))$variables[$name]=TRUE;
  $columns[$m[1]]=$tree;
 }
 if(!$columns||count($variables)>6)throw new InvalidArgumentException('Truth table requires expressions with at most six input variables');
 $vars=array_keys($variables);sort($vars,SORT_NATURAL);$rows=[];
 for($i=0;$i<2**count($vars);$i++){$values=[];foreach($vars as $j=>$var)$values[$var]=(bool)(($i>>(count($vars)-$j-1))&1);foreach($columns as $name=>$tree)$values[$name]=(bool)padKarnaughEval($tree,$values);$rows[]=$values;}
 return ['variables'=>$vars,'columns'=>array_keys($columns),'rows'=>$rows];
}
function padTruthHtml($data,$title='Truth table') {
 $e=fn($s)=>htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
 $html='<table class="pad-truth"><caption>'.$e($title).'</caption><thead><tr>';
 foreach([...$data['variables'],...$data['columns']] as $name)$html.='<th scope="col">'.$e($name).'</th>';
 $html.='</tr></thead><tbody>';foreach($data['rows'] as $row){$html.='<tr>';foreach($row as $value)$html.='<td>'.($value?'true':'false').'</td>';$html.='</tr>';}
 return $html.'</tbody></table>';
}
