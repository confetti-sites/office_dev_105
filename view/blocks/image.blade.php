@php($m = extendModel($model)->label('Image test')))

<picture>{{ $m->image('image_without_decoration')->getSourcesHtml() }}</picture>
<picture>
    <source media="(min-width: 640px)" srcset="giraffe.jpeg 1x" />
    <source srcset="giraffe.small.jpeg 1x, giraffe.small_2x.jpeg 2x" />
    <img src="giraffe.jpeg" alt="" />
</picture>

<picture>{{ $m->image('image_small')->widthPx(1200)->getSourcesHtml() }}</picture>
<img src="giraffe.jpeg" alt="" />

<picture>{{ $m->image('image')->label('Image')->widthPx(1200)->ratio(1, 1)->getSourcesHtml() }}</picture>
<picture>
    <source media="(min-width: 640px)" srcset="giraffe.jpeg 1x, giraffe_2x.jpeg 2x" />
    <source srcset="giraffe.small.jpeg 1x, giraffe.small_2x.jpeg 2x" />
    <img src="giraffe.jpeg" alt="" />
</picture>

<picture>{{ $m->image('image_test')->label('The test image')->widthPx(1200)->ratio(1, 1)->getSourcesHtml() }}</picture>
