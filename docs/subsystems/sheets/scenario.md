# sheet:scenario

`model=` is an A1-addressed scalar/formula map. `scenarios=` maps names to input overrides; `inputs=` and `outputs=` are explicit address lists. Each scenario copies and recalculates the model using the existing server formula engine and same seed. Error codes remain visible; base state is unchanged. Overrides cannot add formulas or undeclared cells. Bounds: 50 scenarios, 100 outputs, 20000 model-cell evaluations across scenarios. Existing evaluator bounds also apply.

## Example

The paired PHP page supplies the application data:

```php
<?php
$scenarioModel=['A1'=>10,'B1'=>3,'C1'=>'=A1*B1'];$scenarioCases=['Low'=>['A1'=>5],'Base'=>[],'High'=>['A1'=>20]];$scenarioInputs=['A1'];$scenarioOutputs=['C1'];
```

The PAD template renders it:

```pad
{sheet:scenario model='scenarioModel', scenarios='scenarioCases', inputs='scenarioInputs', outputs='scenarioOutputs'/}
```

Run the focused fixture with `php tests/subsystems/sheet-scenario.php`.

Array-valued options accept a literal array or the quoted name of an application array/store.
