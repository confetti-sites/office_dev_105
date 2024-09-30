@php($footer = extendModel($model)->label('Select options'))
@php($footer->selectFile('value')->match(['/view/features_select_file/*.blade.php'])->label('Select with option')->default('/view/features_select_file/second.blade.php'))
