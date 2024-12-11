@php($feature = extendModel($model)->label('Select basic'))

{{ $feature->text('first_field') }}
@php($file = $feature->selectFile('value')->match(['/website/includes/features/blade_files/*.blade.php']))

{{ $feature->text('second_field') }}