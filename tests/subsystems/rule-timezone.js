const fs = require('node:fs'), vm = require('node:vm'), cp = require('node:child_process');
const root = require('node:path').resolve(__dirname, '../..');
let rule;
vm.runInNewContext(fs.readFileSync(root + '/www/assets/rules/timezone.js', 'utf8'), {addRule: (name, spec) => rule = spec});
const argument = cp.execFileSync("php", ["-r", "$r = include $argv[1]; echo $r['client']('');", root + "/pad/subs/inputs/rules/types/timezone.php"], {encoding:"utf8"});
for (const [value,want] of Object.entries(JSON.parse(fs.readFileSync(__dirname + '/rule-timezone.json'))))
  if (rule.check(value, argument) !== want) throw Error('Failed: ' + value);
console.log('rule:timezone browser fixtures passed');
