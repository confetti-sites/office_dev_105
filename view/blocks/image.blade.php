@php($m = extendModel($model)->label('Image')))
@php($image1 = $m->image('image')->label('Image')->widthPx(1200)->ratio(1, 1))

<picture>{{ $image1->getSource() }}</picture>
<picture>
    <source media="(min-width: 641px)" srcset="image-standard.jpeg 1x, image-standard_2x.jpeg 2x" />
    <source srcset="small-image.jpeg 1x, small-image_2x.jpeg 2x" />
    <img src="image-standard.jpeg" alt="" />
</picture>
