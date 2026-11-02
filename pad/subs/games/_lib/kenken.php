<?php
function padKenkenDefault(){return ['size'=>4,'cages'=>[
 ['cells'=>[0,1],'op'=>'+','target'=>3],['cells'=>[2,3],'op'=>'+','target'=>7],['cells'=>[4,8],'op'=>'+','target'=>5],['cells'=>[5,6],'op'=>'+','target'=>7],['cells'=>[7,11],'op'=>'+','target'=>3],['cells'=>[9,13],'op'=>'+','target'=>5],['cells'=>[10,14],'op'=>'+','target'=>3],['cells'=>[12],'op'=>'=','target'=>4],['cells'=>[15],'op'=>'=','target'=>3]]];}
function padKenkenCheck($puzzle){
 if(!is_array($puzzle)||!is_int($puzzle['size']??NULL)||$puzzle['size']<3||$puzzle['size']>6||!is_array($puzzle['cages']??NULL))throw new InvalidArgumentException('KenKen size must be 3..6 with authored cages');$n=$puzzle['size'];$seen=[];
 foreach($puzzle['cages'] as $cage){$cells=$cage['cells']??NULL;$op=$cage['op']??NULL;$target=$cage['target']??NULL;if(!is_array($cells)||!array_is_list($cells)||!$cells||count($cells)>6||!in_array($op,['+','*','-','/','='],TRUE)||!is_int($target)||$target<0||$target>1000000000)throw new InvalidArgumentException('Invalid arithmetic cage');if(($op==='='&&count($cells)!==1)||(in_array($op,['-','/'],TRUE)&&count($cells)!==2))throw new InvalidArgumentException('Single/equality and binary arithmetic cage sizes must match');foreach($cells as $i){if(!is_int($i)||$i<0||$i>=$n*$n||isset($seen[$i]))throw new InvalidArgumentException('Cages must partition the board');$seen[$i]=TRUE;}$connected=[$cells[0]];for($i=0;$i<count($connected);$i++)foreach($cells as $cell)if(!in_array($cell,$connected,TRUE)&&abs(intdiv($connected[$i],$n)-intdiv($cell,$n))+abs($connected[$i]%$n-$cell%$n)===1)$connected[]=$cell;if(count($connected)!==count($cells))throw new InvalidArgumentException('Cage cells must be edge connected');}
 if(count($seen)!==$n*$n)throw new InvalidArgumentException('Cages must cover every cell');return $puzzle;
}
function padKenkenCage($values,$cage){
 $filled=array_filter($values,fn($v)=>$v!==0);$complete=count($filled)===count($values);$target=$cage['target'];return match($cage['op']){
 '+'=>$complete?array_sum($values)===$target:array_sum($filled)<=$target,
 '*'=>$complete?array_product($values)===$target:($target>0&&array_product($filled)<=$target&&$target%max(1,array_product($filled))===0),
 '='=>!$complete||$values[0]===$target,
 '-'=>!$complete||abs($values[0]-$values[1])===$target,
 '/'=>!$complete||(min($values)>0&&max($values)===min($values)*$target)};
}
function padKenkenSolve($puzzle,$budget=100000){
 $puzzle=padKenkenCheck($puzzle);if(!is_int($budget)||$budget<1||$budget>1000000)throw new InvalidArgumentException('KenKen solver budget 1..1000000');$n=$puzzle['size'];$grid=array_fill(0,$n*$n,0);$map=[];foreach($puzzle['cages'] as $c=>$cage)foreach($cage['cells'] as $i)$map[$i]=$c;$nodes=0;$solutions=[];$exhausted=FALSE;
 $walk=function($i)use(&$walk,&$grid,&$nodes,&$solutions,&$exhausted,$budget,$puzzle,$n,$map){if(count($solutions)>=2||$exhausted)return;if(++$nodes>$budget){$exhausted=TRUE;return;}if($i===$n*$n){$solutions[]=$grid;return;}$cage=$puzzle['cages'][$map[$i]];for($v=1;$v<=$n;$v++){$ok=TRUE;for($k=0;$k<$n;$k++)if($grid[intdiv($i,$n)*$n+$k]===$v||$grid[$k*$n+$i%$n]===$v){$ok=FALSE;break;}if(!$ok)continue;$grid[$i]=$v;if(padKenkenCage(array_map(fn($cell)=>$grid[$cell],$cage['cells']),$cage))$walk($i+1);$grid[$i]=0;}};$walk(0);
 return ['status'=>$exhausted?'budget':(count($solutions)===0?'unsolved':(count($solutions)===1?'unique':'multiple')),'solution'=>$solutions[0]??NULL,'nodes'=>$nodes];
}
