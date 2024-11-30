@php($feature = extendModel($model)->label('Image widthPx'))
<picture>{!! $feature->image('value')->label('Banner')->widthPx(400)->getSourcesHtml() !!}</picture>