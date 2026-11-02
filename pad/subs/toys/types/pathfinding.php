<?php
return padToy('pathfinding','Pathfinding algorithms',['width'=>padToyInt('width',18,5,30),'height'=>padToyInt('height',10,5,30),'algorithm'=>padToyChoice('algorithm','astar',['bfs','dijkstra','astar'])]);
?>
