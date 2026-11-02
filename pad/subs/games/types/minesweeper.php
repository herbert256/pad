<?php
$size=padGameInt('size',9,5,24);
return padGame('minesweeper','Minesweeper',['size'=>$size,'mines'=>padGameInt('mines',min(10,$size*$size-9),1,$size*$size-9)]);
?>
