<?php
include_once PAD.'subs/games/_lib/sokoban.php';
$levels=padSokobanLevels();$level=padGameInt('level',1,1,count($levels));
$source=trim(padGameSource(),"\r\n");
return padGame('sokoban','Sokoban',['level'=>padSokobanLevel($source!==''?$source:$levels[$level-1])]);
?>
