<?php
// rack - unit-positioned devices with front/rear labels and checked occupancy.
include_once PAD.'subs/diagrams/_lib/rack.php';
$data=padDiagramRackParse(padUnprotectQuotes(padDiagramSource()));
if(is_string($data)){if($padCheckSyntax)padError('the rack diagram: '.$data);return '';}
$side=padTagParm('side','front');if(!in_array($side,['front','rear'],TRUE)){if($padCheckSyntax)padError('rack side must be front or rear');return '';}
return padDiagramRackSvg($data,$side==='rear',(string)padTagParm('title','Equipment rack'));
