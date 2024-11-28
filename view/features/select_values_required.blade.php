@php($feature = extendModel($model)->label('Select required'))
@php($feature->select('value')->options(['First', 'Second'])->required())
