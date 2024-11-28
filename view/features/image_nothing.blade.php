@php($feature = extendModel($model)->label('Image nothing'))
<picture>{!! $feature->image('value')->getSourcesHtml() !!}</picture>
