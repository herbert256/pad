<?php
$visualOptions=[];function padTagParm($name,$default=''){return $GLOBALS['visualOptions'][$name]??$default;}function padError($message){throw new InvalidArgumentException($message);}
require __DIR__.'/_visual_boot.php';require PAD.'subs/palettes/_lib/palette.php';$padCheckSyntax=TRUE;
function rejects($fn,$message){try{$fn();throw new RuntimeException($message);}catch(InvalidArgumentException $e){}}
$visualOptions=['usage'=>'sequential','scheme'=>'dark'];verify(padChartPaletteValidate('viridis',7),'explicit sequential dark');
$visualOptions=['usage'=>'categorical'];verify(padChartPaletteValidate('okabe-ito',8),'categorical supported count');rejects(fn()=>padChartPaletteValidate('okabe-ito',9),'category limit');rejects(fn()=>padChartPaletteValidate('viridis',3),'mismatched type');
$visualOptions=['usage'=>'sequential','scheme'=>'blue'];rejects(fn()=>padChartPaletteValidate('viridis'),'invalid scheme');$visualOptions=['usage'=>['sequential']];rejects(fn()=>padChartPaletteValidate('viridis'),'invalid usage shape');
$visualOptions=[];$base=padChartPaletteRule('viridis',3);verify(padChartPaletteValidate('viridis',3),'default unchanged');$visualOptions=['usage'=>'sequential','scheme'=>'light'];verify($base===padChartPaletteRule('viridis',3),'metadata does not recolor');
echo "chart palette: explicit scale, category count and scheme validation, default styles unchanged\n";
