@php($feature = extendModel($model)->label('Image widthPx'))
<picture>{!! $feature->image('value')->label('The image label')->widthPx(400)->ratio(400, 300)->getSourcesHtml() !!}</picture>