@php($footer = extendModel($model)->label('Image widthPx'))
<picture>{!! $footer->image('value')->widthPx(400)->getSourcesHtml() !!}</picture>