<?php
// Original PAD levels authored for this feature; distributed under the repository license.
function padSokobanLevels(){return ["#######\n#     #\n# .$@ #\n#     #\n#######", "#######\n# . . #\n# $ $ #\n#  @  #\n#     #\n#######", "########\n#      #\n# .$$@ #\n#  .   #\n#      #\n########"];}
function padSokobanLevel($text){
 $rows=explode("\n",str_replace("\r",'',trim($text,"\r\n")));$w=strlen($rows[0]??'');$h=count($rows);
 if($w<3||$w>30||$h<3||$h>30)throw new InvalidArgumentException('Sokoban level must be 3 to 30 cells each way');
 $players=$boxes=$goals=0;
 foreach($rows as $y=>$row){if(strlen($row)!==$w||preg_match('/[^# .$*@+]/',$row))throw new InvalidArgumentException('Invalid Sokoban row');
  for($x=0;$x<$w;$x++){$c=$row[$x];if(($x===0||$y===0||$x===$w-1||$y===$h-1)&&$c!=='#')throw new InvalidArgumentException('Sokoban boundary must be walls');$players+=str_contains('@+',$c)?1:0;$boxes+=str_contains('$*',$c)?1:0;$goals+=str_contains('.*+',$c)?1:0;}
 }
 if($players!==1||$boxes<1||$boxes!==$goals)throw new InvalidArgumentException('Sokoban needs one player and equal boxes/goals');return implode("\n",$rows);
}
?>
