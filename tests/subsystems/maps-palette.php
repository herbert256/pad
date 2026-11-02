<?php
require __DIR__.'/_visual_boot.php';
require PAD.'subs/maps/_lib/map.php';
$plain=padMap('es-regions',['ES-MD'=>[10],'ES-CN'=>[20]],'Regions',600,0);
$colored=padMap('es-regions',['ES-MD'=>[10],'ES-CN'=>[20]],'Regions',600,0,'linear',[],'viridis','dark');
svgValid($colored);verify(!str_contains($plain,'pc-pal-viridis')&&str_contains($colored,'pc-pal-viridis'),'opt-in palette applied');
verify(str_contains($colored,'Canary Islands')&&str_contains($colored,'different scales'),'inset labels and scale description');
foreach([['okabe-ito','light'],['rdbu','light'],['viridis','unknown']] as [$palette,$scheme]){
 try{padMap('world',[],'Map',600,0,'linear',[],$palette,$scheme);}catch(InvalidArgumentException $e){continue;}
 throw new RuntimeException('Incompatible map palette accepted');
}
echo "Map palette: opt-in colors, sequential semantics, scheme rejection and inset labels passed\n";
