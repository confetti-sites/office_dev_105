@php($feature = extendModel($model)->label('Image widthPx'))
<picture>{!! $feature->image('value')->widthPx(400)->getSourcesHtml() !!}</picture>