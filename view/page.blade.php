@php($page = newRoot(new \model\page)->label('Page'))

@foreach($page->list('block')->columns(['list_title'])->get() as $contentRow)
    @php($row = $contentRow->selectFile('row')->match(['/view/blocks/*.blade.php'])->default('/view/blocks/divider.blade.php'))
    @php($contentRow->text('list_title')->label('Internal Title')->help('A title for internal use only.'))
    @include($row->getView(), ['model' => $row])
@endforeach
