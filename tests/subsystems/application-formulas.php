<?php
 define('PAD',dirname(__DIR__,2).'/pad/');function padDirList($p){return scandir($p);}require PAD.'lib/subsystem.php';
 foreach(['formula','collect','functions','dates','format','sheet'] as $lib)require PAD."subs/tables/formulas/_lib/$lib.php";
 function ck($ok,$why){if(!$ok)throw new Exception($why);}
 function make($cells){$s=padSheetNew();foreach($cells as $key=>$v){preg_match('/^([A-Z]+)([0-9]+)$/',$key,$m);padSheetPut($s,padFormulaColumn($m[1]),(int)$m[2],padSheetValue($v,TRUE));}padSheetCompute($s);return $s;}
 function val($s,$c,$r){$v=$s['cells'][$r][$c]??[];return $v['result']??$v['value']??NULL;}
 $s=make(['A1'=>3,'A2'=>1,'A3'=>3,'C1'=>'=SORT(UNIQUE(A1:A3))','E1'=>'=C2*10']);ck(val($s,3,1)===1.0&&val($s,3,2)===3.0&&val($s,5,1)===30.0,'nested sort/unique and spilled dependency');
 $s=make(['A1'=>3,'A2'=>1,'A3'=>3,'C1'=>'=FILTER(A1:A3,A1:A3>1)']);ck(val($s,3,2)===3.0,'filter comparison');
 $s=make(['A1'=>3,'A2'=>1,'C1'=>'=SORT(A1:A2)','C2'=>'occupied']);ck(val($s,3,1)===padFormulaError('#SPILL!'),'collision');
 $s=make(['A1'=>3,'A2'=>1,'A3'=>3,'C1'=>'=UNIQUE(A1:A3)','E1'=>'=C2']);$s['cells'][2][1]=padSheetValue(3,TRUE);padSheetCompute($s);ck(val($s,3,2)===NULL&&val($s,5,1)===0.0,'shrinking recomputation');
 $s=make(['A1'=>1,'A2'=>2,'C1'=>'=FILTER(A1:A2,A1:A2>5)']);ck(val($s,3,1)===padFormulaError('#CALC!'),'empty filter');
 $s=make(['A1'=>1,'A2'=>2,'C1'=>'=FILTER(A1:A2,A1:A2>5,"none")']);ck(val($s,3,1)==='none','empty fallback');
 $s=make(['A1'=>1,'A2'=>2,'B1'=>3,'B2'=>5,'D1'=>'=SLOPE(B1:B2,A1:A2)','D2'=>'=INTERCEPT(B1:B2,A1:A2)','D3'=>'=RSQ(B1:B2,A1:A2)','D4'=>'=COVARIANCE.P(B1:B2,A1:A2)','D5'=>'=COVARIANCE.S(B1:B2,A1:A2)']);
 foreach([1=>2,2=>1,3=>1,4=>.5,5=>1] as $r=>$v)ck(abs(val($s,4,$r)-$v)<1e-12,'paired statistics '.$r);
 $s=make(['A1'=>'=B1+1','B1'=>'=A1+1']);ck(val($s,1,1)===padFormulaError('#REF!'),'scalar cycle');
 $s=make(['A1'=>2,'A2'=>1,'C1'=>'=SORT(A1:A2)','C2'=>'=1+1']);ck(val($s,3,1)===padFormulaError('#SPILL!'),'formula collision');
 $s=make(['A1'=>2,'A2'=>1,'C1'=>'=SORT(A1:A2)']);$s['cells'][2][3]=padSheetValue('block',TRUE);padSheetCompute($s);ck(val($s,3,1)===padFormulaError('#SPILL!')&&val($s,3,2)==='block','collision rollback');unset($s['cells'][2][3]);padSheetCompute($s);ck(val($s,3,1)===1.0&&val($s,3,2)===2.0,'collision recovery');
 $s=make(['A1'=>'=SORT(C1:C2)','C1'=>'=A2','C2'=>2]);ck(val($s,1,1)===padFormulaError('#REF!')&&val($s,3,1)===padFormulaError('#REF!'),'cycle through spill child');
 $s=make(['A1'=>3,'B1'=>1,'C1'=>2,'A3'=>'=SORT(A1:C1,1,1,TRUE)']);ck(val($s,1,3)===1.0&&val($s,3,3)===3.0,'horizontal sort');
 $s=make(['A1'=>3,'B1'=>1,'C1'=>2,'A3'=>'=FILTER(A1:C1,A1:C1>1)']);ck(val($s,1,3)===3.0&&val($s,2,3)===2.0,'horizontal filter');
 $s=make(['A1'=>1,'B1'=>2,'A2'=>1,'B2'=>2,'A3'=>3,'B3'=>4,'D1'=>'=UNIQUE(A1:B3,FALSE,TRUE)']);ck(val($s,4,1)===3.0&&val($s,5,1)===4.0,'exactly once rows');
 $s=make(['A1'=>'=FILTER(B1:B2,C1:C3)']);ck(val($s,1,1)===padFormulaError('#VALUE!'),'mask shape');
 $s=make(['A1'=>'=UNIQUE(B1:B10001)']);ck(val($s,1,1)===padFormulaError('#NUM!'),'array size bound');
 require PAD.'lib/formulas.php';ck(padFormula('=SORT(A1:A2)',['A1'=>2,'A2'=>1])===[[1],[2]],'public matrix export');ck(padSheet(['A1'=>2,'A2'=>1,'C1'=>'=SORT(A1:A2)'])['C2']===2,'public spill export');
 $vector=fn($v)=>['range'=>array_map(fn($n)=>[$n],$v),'w'=>1,'h'=>count($v),'c'=>1,'r'=>1];$x=$vector([1e15,1e15+1,1e15+2]);$y=$vector([1,3,5]);
 foreach(['slope'=>2.0,'rsq'=>1.0,'intercept'=>-1999999999999999.0,'covariance.p'=>4.0/3,'covariance.s'=>2.0] as $name=>$expected){$got=padFormulaType($name)['call']([$y,$x],[]);ck(is_float($got)&&abs($got-$expected)<1e-12,'large-offset kernel '.$name);}
 ck(padFormulaType('slope')['call']([$vector([1,2]),$vector([-1e308,1e308])],[])===padFormulaError('#NUM!'),'shared kernel nonfinite accumulation');
 $x=[0,-1e308,1e308];$y=$x;for($i=0;$i<10;$i++){$x[]=-2e307;$x[]=2e307;$y[]=-1e308;$y[]=1e308;}
 ck(abs(padFormulaType('slope')['call']([$vector($y),$vector($x)],[])-15/7)<1e-12,'slope uses representable scale ratio before product');
 ck(padFormulaType('intercept')['call']([$vector($y),$vector($x)],[])===0.0,'intercept uses representable scale ratio before product');
 foreach(['slope','intercept','rsq'] as $name)ck(padFormulaType($name)['call']([$vector([1e200,-2e200,1e200]),$vector([-1e-200,0,1e-200])],[])===0.0,'zero moment avoids overflowing scale ratio '.$name);
 echo "Application formulas: nested arrays, comparisons, spills, shrinking dependencies, empty results and statistics passed\n";
