@php($footer = extendModel($model)->label('Select nothing'))
@php($footer->select('value')->options(['First', 'Second'])->default('Second'))
