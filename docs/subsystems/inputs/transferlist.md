# input:transferlist

`choices=` maps stable IDs to labels (max 500; IDs letters/digits/_/-), value is an ID list. Native multiple select works without JS; ordered=1 adds numeric positions. JS supplies Available/Chosen lists and Add/Remove/Move buttons, all keyboard accessible. Submit IDs as name[] and positions as name_order[id]. Validate with `padInputTransferList($ids,$choices,$ordered,$positions)`; it rejects duplicate/unknown IDs and invalid/tied positions. Set selections sort IDs; ordered selections retain chosen order. Reset restores defaults.

## Example

The paired PHP page supplies the application data:

```php
<?php
$memberChoices=['ada'=>'Ada','bo'=>'Bo','cy'=>'Cy'];$chosenMembers=['bo'];
```

The PAD template renders it:

```pad
{input:form 'transferlist'}{input:transferlist 'members', choices=$memberChoices, value=$chosenMembers, ordered=1, label='Members'/}<button type='submit'>Save</button><button type='reset'>Reset</button>{/input:form}
```

Run the focused fixture with `php tests/subsystems/input-transferlist.php`.
