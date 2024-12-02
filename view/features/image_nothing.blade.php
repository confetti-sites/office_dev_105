@php($feature = extendModel($model)->label('Image nothing'))
{!! $feature->image('value')->getPicture() !!}
