(function(){'use strict';
 var DT=1/120,W=480,H=320,R=5,PW=80;
 function serve(s){s.x=240;s.y=270;s.vx=100;s.vy=-180;}
 function create(){var s={paddle:200,lives:3,score:0,result:null,bricks:[]};for(var y=0;y<4;y++)for(var x=0;x<8;x++)s.bricks.push({x:12+x*58,y:28+y*22,w:50,h:14,alive:true});serve(s);return s;}
 function step(s,input){if(s.result)return;var oldY=s.y;s.paddle=Math.max(0,Math.min(W-PW,s.paddle+Math.max(-1,Math.min(1,input||0))*270*DT));s.x+=s.vx*DT;s.y+=s.vy*DT;
  if(s.x<R){s.x=R;s.vx=Math.abs(s.vx);}if(s.x>W-R){s.x=W-R;s.vx=-Math.abs(s.vx);}if(s.y<R){s.y=R;s.vy=Math.abs(s.vy);}
  if(s.vy>0&&oldY+R<=294&&s.y+R>=294&&s.x>=s.paddle-R&&s.x<=s.paddle+PW+R){s.y=294-R;var offset=Math.max(-1,Math.min(1,(s.x-s.paddle-PW/2)/(PW/2)));s.vx=offset*180;s.vy=-Math.sqrt(240*240-s.vx*s.vx);}
  for(var b of s.bricks){if(!b.alive||s.x+R<b.x||s.x-R>b.x+b.w||s.y+R<b.y||s.y-R>b.y+b.h)continue;b.alive=false;s.score++;if(oldY+R<=b.y){s.y=b.y-R;s.vy=-Math.abs(s.vy);}else if(oldY-R>=b.y+b.h){s.y=b.y+b.h+R;s.vy=Math.abs(s.vy);}else{if(s.vx>0){s.x=b.x-R;s.vx=-Math.abs(s.vx);}else{s.x=b.x+b.w+R;s.vx=Math.abs(s.vx);}}break;}
  if(s.bricks.every(b=>!b.alive)){s.result='won';return;}if(s.y>H+R){s.lives--;if(!s.lives)s.result='lost';else serve(s);}
 }
 function advance(s,elapsed,input,remainder){var sum=(remainder||0)+Math.max(0,Math.min(.1,elapsed));while(sum+1e-12>=DT&&!s.result){step(s,input);sum-=DT;}return Math.max(0,sum);}
 if(typeof module!=='undefined'&&module.exports)module.exports={create,step,advance,DT};if(typeof PadGames==='undefined')return;
 PadGames.register('breakout',function(k){var s,canvas,ctx,playing=false,raf=0,last=0,acc=0,keys={},control=new AbortController();
  function draw(){ctx.fillStyle='#15202b';ctx.fillRect(0,0,W,H);s.bricks.forEach((b,i)=>{if(b.alive){ctx.fillStyle=['#f3c969','#ed895f','#6ec3b0','#85b2ef'][Math.floor(i/8)];ctx.fillRect(b.x,b.y,b.w,b.h);}});ctx.fillStyle='#f4f3ec';ctx.fillRect(s.paddle,294,PW,9);ctx.beginPath();ctx.arc(s.x,s.y,R,0,Math.PI*2);ctx.fill();canvas.setAttribute('aria-label','Breakout: '+s.score+' of 32 bricks, '+s.lives+' lives');}
  function status(){if(!s)return;k.status((playing?'Playing':'Paused')+' — '+s.score+'/32 bricks, '+s.lives+' lives. Arrow keys or drag to move.');}
  function pause(){playing=false;if(raf)cancelAnimationFrame(raf);raf=0;last=0;keys={};play.textContent='Play';status();}
  function tick(now){raf=0;if(!playing)return;if(!k.root.isConnected){dispose();return;}if(document.hidden){pause();return;}var elapsed=last?(now-last)/1000:0;last=now;var lives=s.lives,score=s.score;acc=advance(s,elapsed,(keys.ArrowRight?1:0)-(keys.ArrowLeft?1:0),acc);draw();if(lives!==s.lives||score!==s.score)status();if(s.result){pause();s.result==='won'?k.won('All 32 bricks cleared!'):k.lost('No lives left. '+s.score+' bricks cleared.');return;}raf=requestAnimationFrame(tick);}
  function start(){if(k.over())reset();if(playing)return;playing=true;last=0;play.textContent='Pause';status();raf=requestAnimationFrame(tick);}
  function reset(){pause();k.fresh();s=create();acc=0;canvas=k.el('canvas','pg-breakout-canvas');canvas.width=W;canvas.height=H;canvas.setAttribute('role','img');canvas.tabIndex=0;ctx=canvas.getContext('2d');k.board.appendChild(canvas);var held=false;function point(e){var r=canvas.getBoundingClientRect();s.paddle=Math.max(0,Math.min(W-PW,(e.clientX-r.left)*W/r.width-PW/2));draw();}canvas.addEventListener('pointerdown',function(e){held=true;try{canvas.setPointerCapture(e.pointerId);}catch(ignore){}point(e);});canvas.addEventListener('pointermove',function(e){if(held)point(e);});canvas.addEventListener('pointerup',()=>{held=false;});canvas.addEventListener('pointercancel',()=>{held=false;});draw();status();}
  var play=k.button('Play',()=>playing?pause():start());k.button('Left',()=>{s.paddle=Math.max(0,s.paddle-28);draw();});k.button('Right',()=>{s.paddle=Math.min(W-PW,s.paddle+28);draw();});k.button('Reset',reset);
  k.keys({ArrowLeft:()=>{keys.ArrowLeft=true;if(!playing){s.paddle=Math.max(0,s.paddle-18);draw();}},ArrowRight:()=>{keys.ArrowRight=true;if(!playing){s.paddle=Math.min(W-PW,s.paddle+18);draw();}},' ':()=>playing?pause():start()});k.root.addEventListener('keyup',e=>{delete keys[e.key];});k.root.addEventListener('focusout',()=>{keys={};});document.addEventListener('visibilitychange',()=>{if(document.hidden)pause();},{signal:control.signal});
  var observer=new MutationObserver(()=>{if(!k.root.isConnected)dispose();});observer.observe(document.documentElement,{childList:true,subtree:true});function dispose(){pause();control.abort();observer.disconnect();}reset();
 });
})();
