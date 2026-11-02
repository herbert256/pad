const a=require('node:assert/strict'),m=require('../../www/assets/toys/optics.js');
const p=m.trace([0,150],0,[{x:100,kind:'lens',focal:100}]);a.equal(p[1][1],150);const slope=(p[2][1]-p[1][1])/(p[2][0]-p[1][0]);a.equal(slope,.5);a.equal(p[1][1]+slope*100,200);
const mirror=m.trace([20,100],.1,[{x:300,kind:'mirror'}]);a.equal(mirror[1][0],300);a.equal(mirror[2][0],20);a.ok(mirror[2][1]>mirror[1][1]);console.log('optics: focal geometry and mirror propagation passed');
