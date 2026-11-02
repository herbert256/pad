(function(){
 function create(){return {board:Array(42).fill(0),turn:1,result:0,moves:0};}
 function drop(s,c){if(s.result||c<0||c>6)return -1;var r=5;while(r>=0&&s.board[r*7+c])r--;if(r<0)return -1;var i=r*7+c,who=s.turn;s.board[i]=who;s.moves++;
 [[0,1],[1,0],[1,1],[1,-1]].forEach(function(d){var n=1;[-1,1].forEach(function(sign){for(var k=1;k<4;k++){var y=r+d[0]*k*sign,x=c+d[1]*k*sign;if(y<0||y>5||x<0||x>6||s.board[y*7+x]!==who)break;n++;}});if(n>=4)s.result=who;});if(!s.result&&s.moves===42)s.result=3;s.turn=3-who;return i;}
 if(typeof module!=='undefined'&&module.exports)module.exports={create:create,drop:drop};if(typeof PadGames==='undefined')return;
 PadGames.register('connectfour',function(k){var s,cells,buttons;
 function play(c){if(drop(s,c)<0)return;draw();}
 function draw(){cells.forEach(function(cell,i){cell.textContent=s.board[i]===1?'●':s.board[i]===2?'○':'·';cell.dataset.player=s.board[i];cell.setAttribute('aria-label','Row '+(Math.floor(i/7)+1)+', column '+(i%7+1)+': '+(s.board[i]?'Player '+s.board[i]:'empty'));});buttons.forEach(function(b,c){b.disabled=!!s.result||s.board[c]!==0;});if(s.result)k.won(s.result===3?'The board is full: draw.':'Player '+s.result+' connects four and wins!');else k.status('Player '+s.turn+' ('+(s.turn===1?'solid':'hollow')+') to play');}
 function fresh(){k.fresh();s=create();var controls=k.el('div','cf-columns');buttons=Array.from({length:7},function(_,c){var b=k.el('button','','↓');b.type='button';b.setAttribute('aria-label','Drop in column '+(c+1));b.addEventListener('click',function(){play(c);});controls.appendChild(b);return b;});k.board.appendChild(controls);var grid=k.el('div','cf-grid');grid.setAttribute('role','group');grid.setAttribute('aria-label','Six rows and seven columns');cells=s.board.map(function(_,i){var cell=k.el('span','cf-cell');grid.appendChild(cell);return cell;});k.board.appendChild(grid);draw();}
 k.button('New game',fresh);k.keys({ArrowLeft:function(e){var i=buttons.indexOf(e.target);if(i<0)return false;buttons[(i+6)%7].focus();},ArrowRight:function(e){var i=buttons.indexOf(e.target);if(i<0)return false;buttons[(i+1)%7].focus();}});fresh();
 });
})();
