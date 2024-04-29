@php($m = extendModel($model)->label('Image')))
@php($image = $m->image('image')->label('Image'))
<img src="{{ $image->get() }}" alt="{{ $image->alt() }}" srcset="{{ $image->srcset() }}" sizes="{{ $image->sizes() }}">
