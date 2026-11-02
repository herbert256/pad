// Mirrors charts/_lib/compositions.php: numeric x, supplied intervals, normalized barycentrics.
(function(C){'use strict';
 C.padChartCompositionData=function(kind,rows,o){
  if(C.count(rows)>1000)return 'at most 1000 rows';var out=[],seen=new Set(),error=null;
  C.values(rows).forEach(function(row,i){if(error)return;row=C.padChartRow(row);var label=C.padChartText(row,o.label||'label')||String(i+1),v;
   if(kind==='lollipop'){v=C.get(row,o.value||'value');if(!C.padChartFinite(v)){error='row '+(i+1)+' needs a finite value';return;}out.push([label,C.num(v)]);return;}
   if(kind==='ternary'){v=['a','b','c'].map(f=>C.get(row,o[f]||f));if(v.some(n=>!C.padChartFinite(n)||C.num(n)<0)){error='row '+(i+1)+' needs three nonnegative finite components';return;}v=v.map(C.num);var sum=v.reduce((a,b)=>a+b,0);if(!Number.isFinite(sum)||sum<=0){error='row '+(i+1)+' has a zero or invalid total';return;}if(o.normalize===false&&Math.abs(sum-1)>1e-9){error='row '+(i+1)+' must total 1 when normalize=false';return;}out.push([label,...v.map(n=>n/sum)]);return;}
   var x=C.get(row,o.x||'x');if(!C.padChartFinite(x)){error='row '+(i+1)+' needs a finite x';return;}x=C.num(x);if(seen.has(x)){error='duplicate x at row '+(i+1);return;}seen.add(x);
   v=['value','low','high'].map(f=>C.get(row,o[f]||f));if(v.some(n=>n!==null&&!C.padChartFinite(n))){error='row '+(i+1)+' needs finite interval values or null';return;}v=v.map(n=>n===null?null:C.num(n));if(!v.includes(null)&&(v[1]>v[2]||v[0]<v[1]||v[0]>v[2])){error='row '+(i+1)+' needs low <= value <= high';return;}out.push([label,x,...v]);
  });if(error)return error;if(kind==='ribbon'&&(o.order||'ascending')==='ascending')out.sort((a,b)=>a[1]-b[1]);return out;
 };
 C.padChartComposition=function(kind,rows,width,height){
  var o={},error=null;['label',...(kind==='ternary'?['a','b','c']:kind==='ribbon'?['x','value','low','high']:['value'])].forEach(function(key){var v=C.padTagParm(key,key);if(typeof v!=='string'&&!Number.isInteger(v))error=key+' must name a field';o[key]=C.str(v);});if(error)return fail(error);
  if(kind==='ribbon'){o.order=C.padTagParm('order','ascending');if(!['ascending','input'].includes(o.order))return fail('order must be ascending or input');}
  if(kind==='ternary'){o.normalize=C.padTagParm('normalize',true);if(typeof o.normalize!=='boolean')return fail('normalize must be true or false');}
  var points=C.padChartCompositionData(kind,rows,o);if(typeof points==='string')return fail(points);if(!points.length)return '';
  var title=C.str(C.padTagParm('title',C.ucfirst(kind))),desc=points.map(p=>p[0]+': '+p.slice(1).map(n=>n===null?'gap':C.padChartNumber(n)).join(', '));
  var svg=C.padChartOpen(kind,points,desc,title,width,height)[1],left=45,right=width-20,top=24,bottom=height-35,pw=Math.max(1,right-left),ph=Math.max(1,bottom-top),n=C.padChartXY,a=C.padChartAttr;
  function line(x1,y1,x2,y2,cls){return '<line class="'+cls+'" x1="'+n(x1)+'" y1="'+n(y1)+'" x2="'+n(x2)+'" y2="'+n(y2)+'"/>';}
  if(kind==='ternary'){
   var mid=(left+right)/2;svg+='<path class="pc-grid" d="M'+n(mid)+' '+n(top)+'L'+n(left)+' '+n(bottom)+'L'+n(right)+' '+n(bottom)+'Z" fill="none"/>';
   [.25,.5,.75].forEach(v=>{svg+=line(mid*v+left*(1-v),bottom-v*ph,mid*v+right*(1-v),bottom-v*ph,'pc-grid');});
   [[mid,top-8,o.a],[left,bottom+20,o.b],[right,bottom+20,o.c]].forEach(p=>{svg+='<text text-anchor="middle" x="'+n(p[0])+'" y="'+n(p[1])+'">'+a(p[2])+'</text>';});
   points.forEach((p,i)=>{svg+='<circle class="pc-c1 pc-ring" cx="'+n(mid*p[1]+left*p[2]+right*p[3])+'" cy="'+n(top*p[1]+bottom*(p[2]+p[3]))+'" r="5"><title>'+a(desc[i])+'</title></circle>';});
  }else{
   var values=kind==='lollipop'?[0]:[];points.forEach(p=>p.slice(kind==='lollipop'?1:2).forEach(v=>{if(v!==null)values.push(v);}));if(!values.length)return '';
   var ticks=C.padChartTicks(Math.min(...values),Math.max(...values),5),lo=ticks[0],hi=ticks[ticks.length-1];if(hi===lo)hi=lo+1;var y=v=>bottom-(v-lo)/(hi-lo)*ph;
   ticks.forEach(t=>{svg+=line(left,y(t),right,y(t),'pc-grid')+'<text text-anchor="end" x="'+n(left-6)+'" y="'+n(y(t)+4)+'">'+C.padChartNumber(t)+'</text>';});
   if(kind==='lollipop'){
    var step=pw/points.length;points.forEach((p,i)=>{var x=left+(i+.5)*step;svg+='<g><title>'+a(desc[i])+'</title>'+line(x,y(0),x,y(p[1]),'pc-l1')+'<circle class="pc-c1" cx="'+n(x)+'" cy="'+n(y(p[1]))+'" r="4"/></g>';if(i%Math.max(1,Math.ceil(60/step))===0)svg+='<text text-anchor="middle" x="'+n(x)+'" y="'+n(bottom+20)+'">'+a(C.padChartFit(p[0],Math.max(20,step-6)))+'</text>';});
   }else{
    var xs=points.map(p=>p[1]),xmin=Math.min(...xs),xmax=Math.max(...xs),x=v=>left+(v-xmin)/(xmax===xmin?1:xmax-xmin)*pw,runs=[],run=[];
    points.forEach(p=>{if(p.slice(2).includes(null)){if(run.length)runs.push(run);run=[];}else run.push(p);});if(run.length)runs.push(run);
    runs.forEach(run=>{var poly=run.map(p=>n(x(p[1]))+','+n(y(p[3]))),estimate=run.map(p=>n(x(p[1]))+','+n(y(p[2])));run.slice().reverse().forEach(p=>poly.push(n(x(p[1]))+','+n(y(p[4]))));svg+='<polygon class="pc-c1" opacity="0.2" points="'+poly.join(' ')+'"/><polyline class="pc-l1" fill="none" points="'+estimate.join(' ')+'"/>';});
    points.forEach((p,i)=>{if(p[2]!==null)svg+='<circle class="pc-c1" cx="'+n(x(p[1]))+'" cy="'+n(y(p[2]))+'" r="3"><title>'+a(desc[i])+'</title></circle>';});
    [...new Set([xmin,xmax])].forEach(t=>{svg+='<text text-anchor="middle" x="'+n(x(t))+'" y="'+n(bottom+20)+'">'+C.padChartNumber(t)+'</text>';});
   }
  }return svg+'</svg>';
 };
 function fail(text){C.padError('the chart: '+text);return '';}
})(typeof window!=='undefined'?window.PadChart:globalThis.PadChart);
