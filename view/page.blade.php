@php($page = newRoot(new \model\page)->label('Page'))

@foreach($page->list('content')->columns(['list_title'])->get() as $contentRow)
    @php($row = $contentRow->selectFile('row')->match(['/view/content/*.blade.php']))
    @php($contentRow->text('list_title')->label('Internal Title')->help('A title for internal use only.'))
    @include($row->getView(), ['model' => $row])
@endforeach
