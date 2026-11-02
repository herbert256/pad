(function(){'use strict';var TAU=2*Math.PI,STEP=1/120;
 // Constant-amplitude ideal cylindrical wave phase; no attenuation or boundary reflections.
 function amplitude(x,y,t,frequency,phase,separation){var a=Math.hypot(x-separation/2,y),b=Math.hypot(x+separation/2,y);return (Math.sin(TAU*(a/2-frequency*t))+Math.sin(TAU*(b/2-frequency*t)+phase))/2;}
 function advance(clock,dt){clock.acc+=Math.max(0,Math.min(.1,dt));while(clock.acc+1e-12>=STEP){clock.steps++;clock.acc-=STEP;}clock.acc=Math.max(0,clock.acc);return clock.steps*STEP;}
 if(typeof module!=='undefined'&&module.exports)module.exports={amplitude,advance};if(typeof PadToys==='undefined')return;
 PadToys.register('waveinterference',function(k,cfg){var ctx=k.canvas(480,400,'Two coherent point sources: amplitude from minus one to one'),f=cfg.frequency,phase=cfg.phase,sep=cfg.separation,clock={steps:0,acc:0},time=0;
  function draw(){for(var y=0;y<80;y++)for(var x=0;x<96;x++){var v=amplitude((x/96-.5)*12,(y/80-.5)*10,time,f,phase*Math.PI/180,sep),strength=Math.abs(v);ctx.fillStyle=v<0?'rgb('+Math.round(245-210*strength)+','+Math.round(245-125*strength)+','+Math.round(245-35*strength)+')':'rgb('+Math.round(245-15*strength)+','+Math.round(245-175*strength)+','+Math.round(245-190*strength)+')';ctx.fillRect(x*5,y*5,5,5);}ctx.fillStyle='#111';ctx.strokeStyle='#fff';[-1,1].forEach(sign=>{ctx.beginPath();ctx.arc(240+sign*sep*20,200,5,0,TAU);ctx.fill();ctx.stroke();});}
  function info(){k.info('Field 12 × 10 m · wavelength 2 m · frequency '+f+' Hz · phase '+phase+'° · blue −1, white 0, red +1. Ideal waves without attenuation.');draw();}
  k.loop(dt=>{time=advance(clock,dt);draw();},draw);k.playButton();k.button('Reset time',()=>{clock={steps:0,acc:0};time=0;draw();});k.slider('Frequency (Hz)',.2,3,.1,f,v=>{f=v;info();});k.slider('Phase (degrees)',0,360,5,phase,v=>{phase=v;info();});k.slider('Source separation (m)',0,8,.1,sep,v=>{sep=v;info();});info();k.begin('Two sources, summed amplitude');
 });
})();
