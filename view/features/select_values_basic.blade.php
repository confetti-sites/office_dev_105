@php($feature = extendModel($model)->label('Select nothing'))
@php($feature->select('value')->options(['First', 'Second']))
