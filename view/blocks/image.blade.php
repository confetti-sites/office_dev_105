@php($m = extendModel($model)->label('Image')))

<picture>{{ $m->image('image_without_decoration')->getSource() }}</picture>
<picture>
    <source media="(min-width: 640px)" srcset="giraffe.jpeg 1x" />
    <source srcset="giraffe.small.jpeg 1x, giraffe.small2x.jpeg 2x" />
    <img src="giraffe.jpeg" alt="" />
</picture>

<picture>{{ $m->image('image_small')->widthPx(1200)->getSource() }}</picture>
<img src="giraffe.jpeg" alt="" />

<picture>{{ $m->image('image')->label('Image')->widthPx(1200)->ratio(1, 1)->getSource() }}</picture>
<picture>
    <source media="(min-width: 640px)" srcset="giraffe.jpeg 1x, giraffe.2x.jpeg 2x" />
    <source srcset="giraffe.small.jpeg 1x, giraffe.small2x.jpeg 2x" />
    <img src="giraffe.jpeg" alt="" />
</picture>
