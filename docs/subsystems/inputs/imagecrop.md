# input:imagecrop

`image=` is trusted server metadata `{id,width,height,orientation}` (EXIF 1..8); `src=` is the preview URL. Coordinates use integer pixels after orientation; orientations 5..8 swap dimensions. Native number controls support keyboard, reset and no-JS submission. JS previews the crop on canvas without reading/uploading pixels. `padInputCrop($posted,$trustedImage)` revalidates image identity and complete bounds and rejects extra fields. Obtain dimensions/EXIF from the real image, never posted metadata; normalize orientation in the image pipeline before applying the returned crop. Persistence is application-owned.

## Example

The paired PHP page supplies the application data:

```php
<?php
$cropImage=['id'=>'pad-logo','width'=>240,'height'=>240,'orientation'=>1];
$cropSrc=$padRoot.'showcase/photos/logo.jpg';
```

The PAD template renders it:

```pad
{input:form 'imagecrop'}{input:imagecrop 'crop', image=$cropImage, src=$cropSrc, label='Crop image'/}<button type='submit'>Apply</button><button type='reset'>Reset</button>{/input:form}
```

Run the focused fixture with `php tests/subsystems/input-imagecrop.php`.
