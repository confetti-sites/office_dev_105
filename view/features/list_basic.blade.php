@php($footer = extendModel($model)->label('List basic'))
@foreach($footer->list('value')->sortable()->get() as $contentRow)
    {{ $contentRow->text('text_of_list') }}
@endforeach
