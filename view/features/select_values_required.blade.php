@php($footer = extendModel($model)->label('Select required'))
@php($footer->select('value')->options(['First', 'Second'])->required())
