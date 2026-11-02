<?php
function padChartStep($rows,$w,$h){
 $align=padChartProcessOption('align','after');$gaps=padChartProcessOption('gaps','break');if(!in_array($align,['before','after','mid'])||!in_array($gaps,['break','connect']))throw new InvalidArgumentException('Step align must be before/after/mid; gaps must be break/connect');
 $p=padChartProcessSeries($rows,padChartProcessOption('x','x'),padChartProcessOption('value','value'));$v=array_values(array_filter(array_column($p,1),fn($v)=>$v!==NULL));if(!$v)return '';
 $desc=array_map(fn($r)=>padChartNumber($r[0]).': '.($r[1]===NULL?'missing':padChartNumber($r[1])),$p);array_unshift($desc,'Step alignment '.$align.', gaps '.$gaps);
 [$svg,$x,$y]=padChartProcessFrame('step',$p,$desc,padChartProcessOption('title','Step chart'),$w,$h,$p[0][0],end($p)[0],min($v),max($v));
 $svg.='<path class="pc-l1" fill="none" d="'.padChartProcessStepPath($p,$x,$y,$align,$gaps).'"/>';
 foreach($p as $r)if($r[1]!==NULL)$svg.='<circle class="pc-c1" cx="'.padChartXY($x($r[0])).'" cy="'.padChartXY($y($r[1])).'" r="3"><title>'.padChartAttr(padChartNumber($r[0]).': '.padChartNumber($r[1])).'</title></circle>';
 return $svg.'</svg>';
}
