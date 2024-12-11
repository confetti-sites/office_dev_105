@php($feature = extendModel($model)->label('Select basic'))
@php($file = $feature->selectFile('value')->match(['/website/includes/features/blade_files/*.blade.php']))