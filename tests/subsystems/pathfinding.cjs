const a=require('node:assert/strict'),m=require('../../www/assets/toys/pathfinding.js');
const w=[1,9,1,1,1,1,1,1,1]; // goal is bottom-right; expensive short top route is avoidable
let bfs=m.solve(3,3,w,'bfs'),d=m.solve(3,3,w,'dijkstra'),star=m.solve(3,3,w,'astar');a.equal(d.pathcost,4);a.equal(star.pathcost,4);a.ok(bfs.pathcost>4);
a.equal(m.solve(3,3,[1,0,1,0,0,1,1,1,1],'astar').path.length,0);
for(let seed=1;seed<=25;seed++){const weights=Array.from({length:100},(_,i)=>i%7===seed%7?5:1);a.equal(m.solve(10,10,weights,'dijkstra').pathcost,m.solve(10,10,weights,'astar').pathcost);}console.log('pathfinding: weighted optimality, BFS distinction, unreachable goal and oracle parity passed');
