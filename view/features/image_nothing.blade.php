@php($footer = extendModel($model)->label('Image nothing'))
<picture>{!! $footer->image('value')->getSourcesHtml() !!}</picture>
