@php($page = newRoot(new \model\page)->label('Page'))

@foreach($page->list('content')->get() as $row)
    @php($data = $row->selectFile('data')->match(['/view/content/*.blade.php']))
    @include($data->getView(), ['model' => $data])
@endforeach

