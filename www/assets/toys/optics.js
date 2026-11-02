(function(){
 // Paraxial thin-lens slope in the direction of travel; plane mirror reverses dx.
 function trace(source,slope,elements,limit=12){var x=source[0],y=source[1],direction=1,points=[[x,y]];
 for(var step=0;step<limit;step++){
  var ahead=elements.filter(e=>(e.x-x)*direction>1e-7).sort((a,b)=>(a.x-b.x)*direction),el=ahead[0],next=el?el.x:(direction>0?620:20);
  y+=slope*Math.abs(next-x);x=next;if(!Number.isFinite(y)||Math.abs(y)>10000)break;points.push([x,y]);if(!el)break;
  if(el.kind==='mirror')direction=-direction;else slope-=(y-200)/el.focal;
 }
 return points;
 }
 if(typeof module!=='undefined'&&module.exports)module.exports={trace:trace};if(typeof PadToys==='undefined')return;
 PadToys.register('optics',function(k,cfg){var ctx=k.canvas(640,400,'Paraxial thin-lens and plane-mirror simulation'),source=[70,180],rays=[],shown=0,second=!!cfg.second,mirror=!!cfg.mirror;
 function elements(){var out=[{x:280,kind:'lens',focal:cfg.focal}];if(second)out.push({x:420,kind:'lens',focal:-120});if(mirror)out.push({x:550,kind:'mirror'});return out;}
 function rebuild(){rays=[];for(var i=-3;i<=3;i++)rays.push(trace(source,i*.045,elements()));shown=0;draw();k.info('Thin lens f='+cfg.focal+'; source ('+source.map(Math.round).join(', ')+'). Paraxial model.');}
 function draw(){ctx.fillStyle=k.color('canvas');ctx.fillRect(0,0,640,400);ctx.strokeStyle=k.color('grid');ctx.beginPath();ctx.moveTo(0,200);ctx.lineTo(640,200);ctx.stroke();
 elements().forEach(function(el){ctx.strokeStyle=k.color(el.kind==='mirror'?'muted':'accent');ctx.lineWidth=el.kind==='mirror'?5:3;ctx.beginPath();ctx.moveTo(el.x,50);ctx.lineTo(el.x,350);ctx.stroke();if(el.kind==='lens'){ctx.fillStyle=k.color('muted');[-1,1].forEach(function(d){ctx.beginPath();ctx.arc(el.x+d*el.focal,200,3,0,Math.PI*2);ctx.fill();});ctx.fillText('f='+el.focal,el.x+5,40);}});
 rays.slice(0,shown).forEach(function(ray,i){ctx.strokeStyle=k.color(i%2?'accent2':'accent3');ctx.lineWidth=1.5;ctx.beginPath();ray.forEach(function(p,j){if(j)ctx.lineTo(p[0],p[1]);else ctx.moveTo(p[0],p[1]);});ctx.stroke();});ctx.fillStyle=k.color('bad');ctx.beginPath();ctx.arc(source[0],source[1],7,0,Math.PI*2);ctx.fill();}
 function step(){shown=Math.min(7,shown+1);draw();if(shown===7){k.pause();k.status('Seven rays traced; drag the source or change the optics');}}
 var elapsed=0;k.loop(function(dt){elapsed+=dt;if(elapsed>.12){elapsed=0;step();}},draw);k.playButton();k.button('Step ray',function(){k.pause();step();});k.button('Reset',function(){source=[70,180];height.set(source[1]);rebuild();});k.slider('Focal length',40,240,5,cfg.focal,function(v){cfg.focal=v;rebuild();});var height=k.slider('Source height',40,360,1,source[1],function(v){source[1]=v;rebuild();});k.check('Diverging second lens',second,function(v){second=v;rebuild();});k.check('Plane mirror',mirror,function(v){mirror=v;rebuild();});
 var drag=false;k.pointer(ctx.canvas,{down:function(p){drag=Math.hypot(p.x-source[0],p.y-source[1])<25;return drag;},move:function(p,e,pressed){if(!drag||!pressed)return;source=[Math.max(25,Math.min(180,p.x)),Math.max(40,Math.min(360,p.y))];height.set(source[1]);rebuild();shown=7;draw();},up:function(){drag=false;}});k.theme(draw);rebuild();k.begin('Tracing seven paraxial rays');
 });
})();
