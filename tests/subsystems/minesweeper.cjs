const assert=require('node:assert/strict'),m=require('../../www/assets/games/minesweeper.js');
let seed=1;const random=()=>((seed=Math.imul(seed,1664525)+1013904223>>>0)/4294967296);
let s=m.create(9,10,random);m.reveal(s,40);assert.equal(s.bombs.filter(Boolean).length,10);for(const i of [40,...m.neighbors(s,40)])assert.ok(!s.bombs[i]);
for(let i=0;i<81;i++)if(!s.bombs[i])m.reveal(s,i);assert.equal(s.result,'won');
s=m.create(9,10,random);m.reveal(s,0);const mine=s.bombs.findIndex(Boolean);m.reveal(s,mine);assert.equal(s.result,'lost');console.log('minesweeper: safe first region, flood-fill, win and loss passed');
