# toys: pathfinding

`{toy:pathfinding algorithm='astar', width=18, height=10, seed=7, paused/}`
compares BFS, Dijkstra and A* on the same bounded grid (up to 30 × 30). Click or
keyboard-activate a cell to cycle floor, wall and cost 5. Start/goal stay fixed.
Arrow keys move a roving focus; Play/Pause, Step and Reset search inspect visited
nodes. Restore map returns the seeded setup. Compare all reports visited count and
actual route cost. BFS minimizes edge count and ignores weights when choosing;
Dijkstra and A* minimize total entered-cell cost. A* uses admissible Manhattan
distance with positive costs. Unreachable goals are reported explicitly.
