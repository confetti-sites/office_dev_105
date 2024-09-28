@php($page = newRoot(new \model\page)->label('Page'))

@foreach($page->list('feature')->sortable()->get() as $contentRow)
    @php($row = $contentRow->selectFile('type')->match(['/view/features/*.blade.php'])->default('/view/features')->useLabelFor('../selected_feature'))
    @php($contentRow->hidden('selected_feature')->label('Type'))
    @include($row->getView(), ['model' => $row])
@endforeach

