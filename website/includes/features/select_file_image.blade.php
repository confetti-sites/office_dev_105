@php($feature = extendModel($model)->label('Select file image'))

@php($file = $feature->selectFile('value')->match(['/website/includes/features/icons/*.svg']))
@include($file->getView(), ['model' => $file])

