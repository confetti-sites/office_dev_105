@php($page = newRoot(new \model\page)->label('Page'))

@foreach($page->list('block')->columns(['admin_title'])->sortable()->get() as $contentRow)
    @php($row = $contentRow->selectFile('row')->match(['/view/blocks/*.blade.php'])->default('/view/blocks/divider.blade.php')->useLabelFor('../admin_title'))
    @php($contentRow->hidden('admin_title'))
    @include($row->getView(), ['model' => $row])
@endforeach
