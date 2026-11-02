(function(){
 function create(text){var rows=text.split('\n'),s={w:rows[0].length,h:rows.length,walls:[],goals:[],boxes:[],player:0,history:[]};rows.join('').split('').forEach(function(c,i){if(c==='#')s.walls.push(i);if('.*+'.includes(c))s.goals.push(i);if('$*'.includes(c))s.boxes.push(i);if('@+'.includes(c))s.player=i;});return s;}
 function move(s,dx,dy){var x=s.player%s.w,y=Math.floor(s.player/s.w),nx=x+dx,ny=y+dy;if(nx<0||ny<0||nx>=s.w||ny>=s.h)return false;var next=ny*s.w+nx;if(s.walls.includes(next))return false;var box=s.boxes.indexOf(next),beyond=next+dy*s.w+dx;if(box>=0&&(nx+dx<0||nx+dx>=s.w||ny+dy<0||ny+dy>=s.h||s.walls.includes(beyond)||s.boxes.includes(beyond)))return false;s.history.push({player:s.player,boxes:s.boxes.slice()});if(s.history.length>1000)s.history.shift();if(box>=0)s.boxes[box]=beyond;s.player=next;return true;}
 function undo(s){var p=s.history.pop();if(p){s.player=p.player;s.boxes=p.boxes;}}
 function won(s){return s.boxes.every(i=>s.goals.includes(i));}
 if(typeof module!=='undefined'&&module.exports)module.exports={create:create,move:move,undo:undo,won:won};if(typeof PadGames==='undefined')return;
 PadGames.register('sokoban',function(k,cfg){var s,grid,cells;
 function draw(){cells.forEach(function(cell,i){var wall=s.walls.includes(i),goal=s.goals.includes(i),box=s.boxes.includes(i),player=s.player===i;cell.textContent=wall?'▉':player?'●':box?(goal?'▣':'□'):goal?'·':'';cell.dataset.kind=wall?'wall':box?'box':goal?'goal':'floor';cell.setAttribute('aria-label','Row '+(Math.floor(i/s.w)+1)+', column '+(i%s.w+1)+': '+(wall?'wall':(player?'player':box?'box':'floor')+(goal?' on goal':'')));});if(won(s))k.won('All boxes are on goals. Puzzle solved!');else k.status(s.boxes.filter(i=>s.goals.includes(i)).length+' of '+s.goals.length+' boxes placed. Arrow keys move; U undoes.');}
 function go(dx,dy){if(!k.over()){move(s,dx,dy);draw();}}
 function fresh(){k.fresh();s=create(cfg.level);grid=k.el('div','sk-grid');grid.tabIndex=0;grid.setAttribute('role','group');grid.setAttribute('aria-label','Sokoban board; arrow keys to move');grid.style.gridTemplateColumns='repeat('+s.w+',2em)';cells=Array.from({length:s.w*s.h},function(){var cell=k.el('span');grid.appendChild(cell);return cell;});k.board.appendChild(grid);draw();}
 function back(){undo(s);k.root.removeAttribute('data-result');k.root.classList.remove('is-won');draw();}
 k.button('↑',function(){go(0,-1);});k.button('←',function(){go(-1,0);});k.button('↓',function(){go(0,1);});k.button('→',function(){go(1,0);});k.button('Undo',back);k.button('Restart',fresh);k.keys({ArrowUp:function(){go(0,-1);},ArrowDown:function(){go(0,1);},ArrowLeft:function(){go(-1,0);},ArrowRight:function(){go(1,0);},u:back,U:back});fresh();
 });
})();
