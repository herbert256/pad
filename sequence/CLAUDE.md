# sequence/

Mathematical sequence generators and sequence processing system.

## Structure
- `sequence/` - Core sequence processing
- `types/` - Individual sequence type implementations (60+ types)
- `build/` - Sequence building logic
- `actions/` - Sequence actions (merge, order, filter)
- `inits/` - Sequence initialization
- `exits/` - Sequence finalization and storage
- `start/` - Sequence start handlers
- `plays/` - Sequence playback
- `options/` - Sequence options

## Sequence Types (`types/`)
Mathematical sequences including:
- Basic: `range`, `list`, `repeat`, `identity`, `random`
- Arithmetic: `add`, `multiply`, `divide`, `substract`, `modulo`
- Number theory: `prime`, `fibonacci`, `lucas`, `mersenne`, `perfect`
- Figurate: `triangular`, `square`, `pentagonal`, `hexagonal`
- Special: `catalan`, `bell`, `pell`, `golomb`, `happy`, `harshad`
- Logical: `and`, `or`, `xor`, `not`, `nand`, `nor`
- And many more...

Each type has a `flags/` subdirectory for sequence flags.

## Usage
Sequences generate iterables that can be used in templates for mathematical/algorithmic content.
