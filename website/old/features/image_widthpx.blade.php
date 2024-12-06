@php($feature = extendModel($model)->label('Image widthPx'))
{!! $feature->image('value')->label('Banner')->widthPx(400)->getPicture() !!}