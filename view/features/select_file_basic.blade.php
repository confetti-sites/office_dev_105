@php($feature = extendModel($model)->label('Select basic'))
@php($feature->selectFile('value')->match(['/view/features_select_file/*.blade.php']))