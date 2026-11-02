// node build-insets.mjs config.json output.json /path/to/NaturalEarth.shp
// Each inset retains its own projection; coordinates cannot be overlaid on this view.
import {readFileSync,writeFileSync,mkdtempSync,rmSync} from 'node:fs';
import {execFileSync} from 'node:child_process';
import {tmpdir} from 'node:os';
import {join,dirname} from 'node:path';
import {fileURLToPath} from 'node:url';
const config=JSON.parse(readFileSync(process.argv[2])),tmp=mkdtempSync(join(tmpdir(),'pad-map-')),here=dirname(fileURLToPath(import.meta.url));
try {
  let n=0;
  const build=cfg=>{const input=join(tmp,`${n++}.json`),output=input+'.out';writeFileSync(input,JSON.stringify(cfg));execFileSync(process.execPath,[join(here,'build-outline.mjs'),input,output,process.argv[4]],{stdio:'inherit'});return JSON.parse(readFileSync(output));};
  const main=build(config.main),top=main._box[3]+20;let x=0,maxHeight=0;main._insets=[];
  for(const [label,cfg] of Object.entries(config.insets)){
    const part=build(cfg),dx=x+15,dy=top+35;
    for(const [code,row] of Object.entries(part.regions)){
      row[3]=row[3].map((v,i)=>Math.round(v+(i%2?dy:dx)));
      for(const k of [4,5])row[k]=row[k].replace(/M(-?\d+),(-?\d+)/g,(_,a,b)=>`M${Math.round(+a+dx)},${Math.round(+b+dy)}`);
      if(main.regions[code])throw Error('duplicate inset region '+code);
      main.regions[code]=row;
    }
    const width=part._box[2]+30,height=part._box[3]+50;
    main._insets.push([x,top,width,height,label]);x+=width+15;maxHeight=Math.max(maxHeight,height);
  }
  main._box=[0,0,Math.max(main._box[2],x),top+maxHeight+10];
  main._source+=' Inset geometries projected and scaled independently; labels identify displaced islands.';
  main._projection+=' Insets use the centers recorded in the source configuration.';
  writeFileSync(process.argv[3],JSON.stringify(main));
}finally{rmSync(tmp,{recursive:true,force:true});}
