@php($feature = extendModel($model)->label('Select file with other text'))

{{ $feature->text('up_selector') }}

@php($file = $feature->selectFile('value')->match(['/website/includes/features/blade_files/*.blade.php']))
@include($file->getView(), ['model' => $file])

{{ $feature->text('down_selector') }}
