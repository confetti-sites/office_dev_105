@php($feature = extendModel($model)->label('Select options'))
@php($feature->selectFile('value')->match(['/website/includes/features/blade_files/*.blade.php'])->required()->label('Select with option')->default('/website/includes/features/blade_files/second.blade.php'))
