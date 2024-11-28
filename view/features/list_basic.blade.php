@php($feature = extendModel($model)->label('List basic'))
@foreach($feature->list('value')->sortable()->get() as $contentRow)
    {{ $contentRow->text('text_of_list') }}
@endforeach
