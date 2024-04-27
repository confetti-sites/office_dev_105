@php($m = extendModel($model)->label('Image')))
// multiple image sizes, mobile, tablet, desktop
@php($image = $m->image('image')->label('Image'))
<img src="{{ $image->get() }}" alt="{{ $image->alt() }}" srcset="{{ $image->srcset() }}" sizes="{{ $image->sizes() }}">
