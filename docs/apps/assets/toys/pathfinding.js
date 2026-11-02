(function(){
 function create(w,h,weights,algorithm){return {w:w,h:h,weights:weights.slice(),algorithm:algorithm,frontier:[{i:0,cost:0,priority:0}],cost:{0:0},came:{},visited:[],closed:new Set(),done:false,path:[],pathcost:null};}
 function step(s){if(s.done)return;var item;
 do{if(!s.frontier.length){s.done=true;return;}if(s.algorithm!=='bfs')s.frontier.sort((a,b)=>a.priority-b.priority||a.i-b.i);item=s.frontier.shift();}while(s.closed.has(item.i));
 var i=item.i;s.closed.add(i);s.visited.push(i);var goal=s.w*s.h-1;
 if(i===goal){s.done=true;s.path=[i];while(i!==0){i=s.came[i];s.path.unshift(i);}s.pathcost=s.path.slice(1).reduce((sum,i)=>sum+s.weights[i],0);return;}
 var x=i%s.w,y=Math.floor(i/s.w);[[1,0],[0,1],[-1,0],[0,-1]].forEach(function(d){var nx=x+d[0],ny=y+d[1];if(nx<0||ny<0||nx>=s.w||ny>=s.h)return;var n=ny*s.w+nx;if(!s.weights[n]||s.closed.has(n))return;var cost=s.cost[i]+(s.algorithm==='bfs'?1:s.weights[n]);if(s.cost[n]!==undefined&&cost>=s.cost[n])return;s.cost[n]=cost;s.came[n]=i;var heuristic=s.algorithm==='astar'?(s.w-1-nx+s.h-1-ny):0;s.frontier.push({i:n,cost:cost,priority:cost+heuristic});});
 }
 function solve(w,h,weights,algorithm){var s=create(w,h,weights,algorithm);for(var i=0;i<=w*h&&!s.done;i++)step(s);return s;}
 if(typeof module!=='undefined'&&module.exports)module.exports={create:create,step:step,solve:solve};if(typeof PadToys==='undefined')return;
 PadToys.register('pathfinding',function(k,cfg){var weights=Array.from({length:cfg.width*cfg.height},(_,i)=>i===0||i===cfg.width*cfg.height-1?1:k.rand()<.18?0:1),initial=weights.slice(),state,buttons,focus=0,comparison=k.el('p');
 var grid=k.el('div','pf-grid');grid.style.gridTemplateColumns='repeat('+cfg.width+',minmax(1.3em,2em))';k.stage.appendChild(grid);k.stage.appendChild(comparison);
 buttons=weights.map(function(_,i){var b=k.el('button');b.type='button';b.tabIndex=i?-1:0;b.addEventListener('focus',function(){focus=i;});b.addEventListener('click',function(){if(i===0||i===weights.length-1)return;weights[i]=weights[i]===1?0:weights[i]===0?5:1;reset();});grid.appendChild(b);return b;});
 function draw(){var path=new Set(state.path);buttons.forEach(function(b,i){b.textContent=i===0?'S':i===weights.length-1?'G':!weights[i]?'#':weights[i]===5?'5':path.has(i)?'●':state.closed.has(i)?'·':'';b.dataset.kind=path.has(i)?'path':!weights[i]?'wall':state.closed.has(i)?'visited':'free';b.setAttribute('aria-label','Row '+(Math.floor(i/cfg.width)+1)+', column '+(i%cfg.width+1)+': '+(i===0?'start':i===weights.length-1?'goal':!weights[i]?'wall':'cost '+weights[i]));});k.info(cfg.algorithm+' · visited '+state.visited.length+' · path cost '+(state.pathcost===null?'—':state.pathcost));}
 function reset(){k.pause();state=create(cfg.width,cfg.height,weights,cfg.algorithm);comparison.textContent='';draw();k.status('Click cells to cycle floor, wall and cost 5. BFS minimizes steps; Dijkstra and A* minimize cost.');}
 function advance(){step(state);draw();if(state.done){k.pause();k.status(state.path.length?'Path found. Cost '+state.pathcost:'No path reaches the goal.');}}
 var elapsed=0;k.loop(function(dt){elapsed+=dt;if(elapsed>.035){elapsed=0;advance();}},draw);k.playButton();k.button('Step',function(){k.pause();advance();});k.button('Reset search',reset);k.button('Restore map',function(){weights=initial.slice();reset();});k.select('Algorithm',['bfs','dijkstra','astar'],cfg.algorithm,function(v){cfg.algorithm=v;reset();});k.button('Compare all',function(){k.pause();comparison.textContent=['bfs','dijkstra','astar'].map(function(a){var s=solve(cfg.width,cfg.height,weights,a);return a+': '+s.visited.length+' visited, '+(s.path.length?'cost '+s.pathcost:'no path');}).join(' · ');});
 function navigate(e,delta){if(!grid.contains(e.target))return false;focus=Math.max(0,Math.min(weights.length-1,focus+delta));buttons.forEach((b,i)=>b.tabIndex=i===focus?0:-1);buttons[focus].focus();}
 k.keys({ArrowLeft:e=>navigate(e,-1),ArrowRight:e=>navigate(e,1),ArrowUp:e=>navigate(e,-cfg.width),ArrowDown:e=>navigate(e,cfg.width)});reset();k.begin();
 });
})();
