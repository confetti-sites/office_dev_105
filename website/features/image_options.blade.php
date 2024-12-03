@php($feature = extendModel($model)->label('Image widthPx'))
{!! $feature->image('value')->label('The image label')->widthPx(400)->ratio(400, 300)->getPicture() !!}