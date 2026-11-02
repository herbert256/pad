const fs = require('node:fs'), vm = require('node:vm'), cp = require('node:child_process');
const root = require('node:path').resolve(__dirname, '../..');
let rule;
vm.runInNewContext(fs.readFileSync(root + '/www/assets/rules/semver.js', 'utf8'), {addRule: (name, spec) => rule = spec});
const argument = '';
for (const [value,want] of Object.entries(JSON.parse(fs.readFileSync(__dirname + '/rule-semver.json'))))
  if (rule.check(value, argument) !== want) throw Error('Failed: ' + value);
console.log('rule:semver browser fixtures passed');
