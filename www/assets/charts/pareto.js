(function(C){'use strict';
C.padChartPareto=function(rows,width,height){
 var guess=C.padChartGuess(rows,true),used=[],label=C.padChartPick('label',guess[1],used),field=C.padChartPick('value',guess[0],used);
 if(field==='')return '';var points=[],omitted=0;
 C.values(rows).forEach(function(row){row=C.padChartRow(row);var v=C.get(row,field);if(!C.padChartFinite(v)||C.num(v)<0){omitted++;return;}points.push([C.padChartText(row,label),C.num(v),points.length]);});
 if(!points.length)return '';points.sort((a,b)=>b[1]-a[1]||a[2]-b[2]);
 var largest=Math.max(...points.map(p=>p[1])),scaled=0;points.forEach(p=>scaled+=largest?p[1]/largest:0);
 var desc=['Negative and missing contributions omitted: '+omitted],sum=0;
 points.forEach(function(p){sum+=largest?p[1]/largest:0;p[2]=scaled?sum/scaled:0;desc.push(p[0]+': '+C.padChartNumber(p[1])+', cumulative '+C.padChartNumber(C.round(p[2]*100,1))+'%');});
 var svg=C.padChartOpen('pareto',points,desc,C.str(C.padTagParm('title','Pareto contributions')),width,height)[1];
 var left=55,right=width-45,top=24,base=height-46,h=Math.max(10,base-top),step=Math.max(1,(right-left)/points.length),path='';
 [0,0.25,0.5,0.75,1].forEach(function(f){var y=C.padChartXY(base-f*h);svg+='<line class="pc-grid" x1="'+left+'" x2="'+right+'" y1="'+y+'" y2="'+y+'"/><text x="'+(left-5)+'" y="'+C.padChartXY(base-f*h+4)+'" text-anchor="end">'+C.padChartNumber(largest*f)+'</text><text x="'+(right+5)+'" y="'+C.padChartXY(base-f*h+4)+'">'+(f*100)+'%</text>';});
 points.forEach(function(p,i){var x=left+(i+0.5)*step,bar=largest?p[1]/largest*h:0,tip=C.padChartAttr(desc[i+1]);svg+='<rect class="pc-c1" x="'+C.padChartXY(left+i*step+1)+'" y="'+C.padChartXY(base-bar)+'" width="'+C.padChartXY(Math.max(0.5,step-2))+'" height="'+C.padChartXY(bar)+'"><title>'+tip+'</title></rect>';path+=(i?'L':'M')+C.padChartXY(x)+','+C.padChartXY(base-p[2]*h);if(step>=30)svg+='<text x="'+C.padChartXY(x)+'" y="'+(base+16)+'" text-anchor="middle">'+C.padChartAttr(C.padChartFit(p[0],step-4))+'</text>';});
 return svg+'<path class="pc-l2" d="'+path+'" fill="none" stroke-width="2"/><text x="'+left+'" y="'+(height-5)+'">Contribution / cumulative percentage</text></svg>';
};})(typeof window!=='undefined'?window.PadChart:globalThis.PadChart);
