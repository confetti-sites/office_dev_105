@php($footer = extendModel($model)->label('Image widthPx'))
<picture>{!! $footer->image('value')->label('The image label')->widthPx(400)->ratio(400, 300)->getSourcesHtml() !!}</picture>