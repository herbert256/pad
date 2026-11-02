// Browser PHP has no yaml extension. Parse the YAML data pane locally with the pinned
// js-yaml reader, keeping PAD's boolean and numeric conventions (pad/lib/data.php).
import { load, FAILSAFE_SCHEMA, Type, types } from './vendor/js-yaml.mjs';

const integer = new Type('tag:yaml.org,2002:int', {
  kind: 'scalar', resolve: types.int.resolve,
  construct(value) {
    if (/^[-+]?(0|[1-9][0-9]*)$/.test(value) || /^0x[0-9a-fA-F]+$/.test(value)) {
      const number = Number(value);
      return Number.isSafeInteger(number) ? number : value;
    }
    return value;
  }
});
const float = new Type('tag:yaml.org,2002:float', {
  kind: 'scalar', resolve: types.float.resolve,
  construct(value) {
    if (/^[-+]?(\.[0-9]+|(0|[1-9][0-9]*)(\.[0-9]*)?)([eE][-+]?[0-9]+)?$/.test(value)) return Number(value);
    return value;
  }
});
const schema = FAILSAFE_SCHEMA.extend({ implicit: [types.null, types.bool, integer, float, types.merge] });

export function yamlJSON(text) {
  const value = load(text, { schema });
  if (!value || typeof value !== 'object') throw new Error('The YAML data must be a mapping or a list.');
  // Cyclic aliases cannot be represented as the data record passed to PHP.
  return JSON.stringify(value);
}
