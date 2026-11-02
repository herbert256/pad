(function(){
 function deriv(s,p){return [p.sigma*(s[1]-s[0]),s[0]*(p.rho-s[2])-s[1],s[0]*s[1]-p.beta*s[2]];}
 function rk4(s,p,h){var add=(a,b,f)=>a.map((v,i)=>v+b[i]*f),a=deriv(s,p),b=deriv(add(s,a,h/2),p),c=deriv(add(s,b,h/2),p),d=deriv(add(s,c,h),p);return s.map((v,i)=>v+h*(a[i]+2*b[i]+2*c[i]+d[i])/6);}
 if(typeof module!=='undefined'&&module.exports)module.exports={deriv:deriv,rk4:rk4};if(typeof PadToys==='undefined')return;
 PadToys.register('lorenz',function(k,cfg){var W=640,H=420,ctx=k.canvas(W,H,'Lorenz attractor numerical simulation'),state,start=[1+k.rand()*.01,1,1],trail=[],time=0,projection=cfg.projection;
 function reset(){state=start.slice();trail=[state.slice()];time=0;draw();k.status('Reset to the same seeded initial state');}
 function step(){var next=rk4(state,cfg,cfg.dt);if(next.some(v=>!Number.isFinite(v)||Math.abs(v)>1e6)){k.pause();k.status('Numerical divergence detected. Reset or reduce the timestep.');return false;}state=next;time+=cfg.dt;trail.push(state.slice());if(trail.length>cfg.trail)trail.shift();return true;}
 function draw(){ctx.fillStyle=k.color('canvas');ctx.fillRect(0,0,W,H);ctx.strokeStyle=k.color('accent');ctx.lineWidth=1.1;ctx.beginPath();var a=projection==='yz'?1:0,b=projection==='xy'?1:2;trail.forEach(function(s,i){var x=W/2+s[a]*7,y=H/2-(s[b]-(b===2?25:0))*7;if(i)ctx.lineTo(x,y);else ctx.moveTo(x,y);});ctx.stroke();ctx.fillStyle=k.color('ink');ctx.fillText(projection[0]+' / '+projection[1]+' projection',12,20);k.info('dt '+cfg.dt+' · t '+time.toFixed(3)+' · x '+state[0].toFixed(3)+' · y '+state[1].toFixed(3)+' · z '+state[2].toFixed(3));}
 k.loop(function(){for(var i=0;i<5;i++)if(!step())break;draw();},draw);k.playButton();k.button('Step',function(){k.pause();step();draw();});k.button('Reset',reset);k.select('Projection',['xy','xz','yz'],projection,function(v){projection=v;draw();});k.slider('Timestep',.001,.02,.001,cfg.dt,function(v){cfg.dt=v;draw();});k.slider('Rho',1,60,1,cfg.rho,function(v){cfg.rho=v;reset();});k.theme(draw);reset();k.begin('Lorenz simulation running; five integration steps per frame');
 });
})();
