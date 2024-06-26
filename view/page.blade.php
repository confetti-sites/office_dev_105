@php($page = newRoot(new \model\page)->label('Page'))

@foreach($page->list('block')->columns(['selected_block', 'type-/title'])->sortable()->get() as $contentRow)
    @php($row = $contentRow->selectFile('type')->match(['/view/blocks/*.blade.php'])->default('/view/blocks/title.blade.php')->useLabelFor('../selected_block'))
    @php($contentRow->hidden('selected_block')->label('Type'))
    @include($row->getView(), ['model' => $row])
@endforeach
