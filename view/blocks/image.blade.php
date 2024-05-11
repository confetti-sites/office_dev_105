@php($m = extendModel($model)->label('Image')))
@php($image1 = $m->image('image_no_ratio')->label('Image no ratio'))
<img src="{{ $image1->get() }}" alt="{{ $image1->alt() }}" srcset="{{ $image1->srcset() }}" sizes="{{ $image1->sizes() }}">

@php($image2 = $m->image('image_16')->label('Image 16:9')->ratio(16, 9))
<img src="{{ $image2->get() }}" alt="{{ $image2->alt() }}" srcset="{{ $image2->srcset() }}" sizes="{{ $image2->sizes() }}">

@php($image3 = $m->image('image_4')->label('Image 4:3')->ratio(4, 3))
<img src="{{ $image3->get() }}" alt="{{ $image3->alt() }}" srcset="{{ $image3->srcset() }}" sizes="{{ $image3->sizes() }}">