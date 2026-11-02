const a=require('node:assert/strict'),m=require('../../www/assets/games/sokoban.js');
let s=m.create('########\n#      #\n# .$$@ #\n#  .   #\n#      #\n########');[[0,-1],[-1,0],[0,1],[1,0],[0,1],[-1,0],[0,-1],[-1,0]].forEach(d=>m.move(s,...d));a.ok(m.won(s));
s=m.create('#######\n#     #\n# .$@ #\n#     #\n#######');a.equal(m.move(s,-1,0),true);a.equal(m.won(s),true);m.undo(s);a.equal(m.won(s),false);a.equal(s.player,18);
s=m.create('########\n#      #\n# .$$@ #\n#  .   #\n#      #\n########');[[0,-1],[-1,0],[0,1],[1,0],[0,1],[-1,0],[0,-1],[-1,0]].forEach(d=>m.move(s,...d));a.ok(m.won(s));
s=m.create('#######\n# . . #\n# $ $ #\n#  @  #\n#     #\n#######');[[-1,0],[0,-1],[0,1],[1,0],[1,0],[0,-1]].forEach(d=>m.move(s,...d));a.ok(m.won(s));
s=m.create('########\n#      #\n# .$$@ #\n#  .   #\n#      #\n########');[[0,-1],[-1,0],[0,1],[1,0],[0,1],[-1,0],[0,-1],[-1,0]].forEach(d=>m.move(s,...d));a.ok(m.won(s));
s=m.create('#####\n#@$$#\n# ..#\n#####');a.equal(m.move(s,1,0),false);a.equal(m.move(s,0,-1),false);console.log('sokoban: authored solutions, blocked pushes and undo passed');
