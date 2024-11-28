@php($feature = extendModel($model)->label('Bool basic'))
@php($feature->bool('value')->default(false)->label('The Boolean Value')->help('The help of the boolean'))
