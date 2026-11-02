const a=require('node:assert/strict'),m=require('../../www/assets/games/connectfour.js');
let s=m.create();a.equal(m.drop(s,0),35);a.equal(m.drop(s,0),28);
s=m.create();[0,1,0,1,0,1,0].forEach(c=>m.drop(s,c));a.equal(s.result,1);a.equal(m.drop(s,2),-1);
s=m.create();[0,0,1,1,2,2,3].forEach(c=>m.drop(s,c));a.equal(s.result,1);
s=m.create();s.board[35]=s.board[29]=s.board[23]=1;s.board[38]=s.board[31]=s.board[24]=2;a.equal(m.drop(s,3),17);a.equal(s.result,1);
s=m.create();for(let i=0;i<6;i++)m.drop(s,0);a.equal(m.drop(s,0),-1);console.log('connectfour: gravity, full columns and three win directions passed');
