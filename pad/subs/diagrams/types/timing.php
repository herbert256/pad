<?php
include_once PAD.'subs/diagrams/_lib/timing.php';
$data=padDiagramTimingParse(padUnprotectQuotes(padDiagramSource()));if(is_string($data)){if($padCheckSyntax)padError('the timing diagram: '.$data);return '';}
$tick=padTagParm('tick',1);if(!padChartFinite($tick)||$tick<=0){if($padCheckSyntax)padError('timing tick must be positive and finite');$tick=1;}
$direction=padDiagramDirection();if(!in_array($direction,['','LR','RL'],TRUE)){if($padCheckSyntax)padError('timing direction must be LR or RL');$direction='LR';}
return padDiagramTimingSvg($data,$tick,(string)padTagParm('unit',''),$direction==='RL',(string)padTagParm('title','Timing diagram'));
