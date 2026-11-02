(function(){
 function create(n,mines,rand){return {n:n,mines:mines,rand:rand,bombs:[],open:[],flags:[],started:false,result:''};}
 function neighbors(s,i){var out=[],r=Math.floor(i/s.n),c=i%s.n;for(var y=-1;y<=1;y++)for(var x=-1;x<=1;x++)if((x||y)&&r+y>=0&&r+y<s.n&&c+x>=0&&c+x<s.n)out.push((r+y)*s.n+c+x);return out;}
 function count(s,i){return neighbors(s,i).filter(function(j){return s.bombs[j];}).length;}
 function reveal(s,i){if(s.result||s.open[i]||s.flags[i])return;
 if(!s.started){var safe=new Set([i].concat(neighbors(s,i))),spots=[];for(var j=0;j<s.n*s.n;j++)if(!safe.has(j))spots.push(j);for(var p=spots.length-1;p>0;p--){var q=Math.floor(s.rand()*(p+1)),v=spots[p];spots[p]=spots[q];spots[q]=v;}spots.slice(0,s.mines).forEach(function(j){s.bombs[j]=true;});s.started=true;}
 if(s.bombs[i]){s.open[i]=true;s.result='lost';return;}
 var todo=[i];while(todo.length){var j=todo.pop();if(s.open[j]||s.flags[j])continue;s.open[j]=true;if(count(s,j)===0)neighbors(s,j).forEach(function(x){if(!s.open[x]&&!s.bombs[x])todo.push(x);});}
 if(s.open.filter(Boolean).length===s.n*s.n-s.mines)s.result='won';
 }
 var model={create:create,neighbors:neighbors,count:count,reveal:reveal};if(typeof module!=='undefined'&&module.exports)module.exports=model;
 if(typeof PadGames==='undefined')return;
 PadGames.register('minesweeper',function(k,cfg){var s,buttons,flag=false,focused=0;
 function name(i){return 'Row '+(Math.floor(i/s.n)+1)+', column '+(i%s.n+1);}
 function draw(){buttons.forEach(function(b,i){var shown=s.open[i]||(s.result==='lost'&&s.bombs[i]),n=shown?count(s,i):0;b.textContent=shown?(s.bombs[i]?'✹':n||'·'):(s.flags[i]?'⚑':'');b.dataset.open=String(!!s.open[i]);b.setAttribute('aria-label',name(i)+', '+(shown?(s.bombs[i]?'mine':n+' neighboring mines'):(s.flags[i]?'flagged':'covered')));});if(s.result==='won')k.won('All safe squares revealed. You win!');else if(s.result==='lost')k.lost('A mine. Start a new game to try again.');else k.status(s.mines+' mines; '+s.flags.filter(Boolean).length+' flags. '+(flag?'Flag mode.':'Reveal mode.'));}
 function act(i,mark){if(s.result)return;if(mark){if(!s.open[i])s.flags[i]=!s.flags[i];}else reveal(s,i);draw();}
 function focus(i){focused=Math.max(0,Math.min(s.n*s.n-1,i));buttons.forEach(function(b,j){b.tabIndex=j===focused?0:-1;});buttons[focused].focus();}
 function fresh(){k.fresh();s=create(cfg.size,cfg.mines,k.rand);focused=0;var board=k.el('div','ms-grid');board.style.gridTemplateColumns='repeat('+s.n+', minmax(1.5em,2.2em))';buttons=Array.from({length:s.n*s.n},function(_,i){var b=k.el('button');b.type='button';b.tabIndex=i?-1:0;b.addEventListener('click',function(){focused=i;act(i,flag);});b.addEventListener('focus',function(){focused=i;});b.addEventListener('contextmenu',function(e){e.preventDefault();act(i,true);});board.appendChild(b);return b;});k.board.appendChild(board);draw();}
 var mode=k.button('Flag mode',function(){flag=!flag;mode.setAttribute('aria-pressed',String(flag));draw();});mode.setAttribute('aria-pressed','false');k.button('New game',fresh);
 k.keys({ArrowLeft:function(){focus(focused-1);},ArrowRight:function(){focus(focused+1);},ArrowUp:function(){focus(focused-s.n);},ArrowDown:function(){focus(focused+s.n);},f:function(){act(focused,true);},F:function(){act(focused,true);}});fresh();
 });
})();
