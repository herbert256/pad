(function(){'use strict';
 function spawn(s,rand){var free=[];s.cells.forEach((v,i)=>{if(!v)free.push(i);});if(!free.length)return false;var i=free[Math.floor(rand()*free.length)];s.cells[i]=rand()<.9?2:4;return true;}
 function create(rand){var s={cells:Array(16).fill(0),score:0,result:null};spawn(s,rand);spawn(s,rand);return s;}
 function slide(row){var v=row.filter(Boolean),out=[],score=0;for(var i=0;i<v.length;i++){if(v[i]===v[i+1]){out.push(v[i]*2);score+=v[i]*2;i++;}else out.push(v[i]);}while(out.length<4)out.push(0);return {row:out,score:score};}
 function move(s,direction,rand){if(s.result||!['left','right','up','down'].includes(direction))return false;var before=s.cells.slice(),score=0;
  for(var line=0;line<4;line++){var ids=[];for(var i=0;i<4;i++)ids.push(direction==='left'?line*4+i:direction==='right'?line*4+3-i:direction==='up'?i*4+line:(3-i)*4+line);var result=slide(ids.map(i=>s.cells[i]));ids.forEach((i,n)=>{s.cells[i]=result.row[n];});score+=result.score;}
  var changed=s.cells.some((v,i)=>v!==before[i]);if(changed){s.score+=score;spawn(s,rand);}settle(s);return changed;
 }
 function settle(s){if(s.cells.some(v=>v>=2048)){s.result='won';return;}if(s.cells.includes(0))return;for(var y=0;y<4;y++)for(var x=0;x<4;x++){var i=y*4+x;if(x<3&&s.cells[i]===s.cells[i+1]||y<3&&s.cells[i]===s.cells[i+4])return;}s.result='lost';}
 if(typeof module!=='undefined'&&module.exports)module.exports={create,slide,move,spawn,settle};if(typeof PadGames==='undefined')return;
 PadGames.register('2048',function(k){var s,rand,grid;
  function draw(){grid.textContent='';s.cells.forEach(function(v,i){var cell=k.el('div','pg-2048-cell',v||'');cell.setAttribute('role','gridcell');cell.setAttribute('aria-label','Row '+(Math.floor(i/4)+1)+', column '+(i%4+1)+': '+(v||'empty'));cell.setAttribute('data-power',v?Math.min(11,Math.log2(v)):0);grid.appendChild(cell);});k.status('Score '+s.score+'. Use arrows, swipe, or direction buttons.');if(s.result==='won')k.won('2048 reached! Score '+s.score);if(s.result==='lost')k.lost('No moves remain. Score '+s.score);}
  function reset(){k.fresh();rand=PadGames.random(k.seed);s=create(rand);grid=k.el('div','pg-2048-grid');grid.setAttribute('role','grid');grid.setAttribute('aria-label','2048 board');k.board.appendChild(grid);draw();}
  var keys={};[['ArrowLeft','left','Left'],['ArrowRight','right','Right'],['ArrowUp','up','Up'],['ArrowDown','down','Down']].forEach(function(p){keys[p[0]]=function(){move(s,p[1],rand);draw();};k.button(p[2],keys[p[0]]);});k.keys(keys);k.swipe(key=>keys[key]());k.button('Reset',reset);reset();
 });
})();
