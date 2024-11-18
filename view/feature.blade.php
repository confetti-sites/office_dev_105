@php($page = newRoot(new \model\feature)->label('Features'))


@foreach($page->list('feature')->columns(['selected_feature', 'type-/value'])->sortable()->get() as $contentRow)
    @php($row = $contentRow->selectFile('type')->match(['/view/features/*.blade.php'])->default('/view/feature')->useLabelFor('../selected_feature'))
    @php($contentRow->hidden('selected_feature')->label('Type'))
    @include($row->getView(), ['model' => $row])
@endforeach
