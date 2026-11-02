<?php

  // pathfinding - Compare breadth-first search, Dijkstra and A star on an editable grid with obstacles and weighted cells.
  //

return padToy ( 'pathfinding', 'Pathfinding algorithms',['width'=>padToyInt('width',18,5,30),'height'=>padToyInt('height',10,5,30),'algorithm'=>padToyChoice('algorithm','astar',['bfs','dijkstra','astar'])]);
?>
