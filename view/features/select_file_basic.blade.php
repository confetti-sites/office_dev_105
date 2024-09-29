@php($footer = extendModel($model)->label('Select basic'))
@php($footer->selectFile('value')->match(['/view/features_select_file/*.blade.php']))