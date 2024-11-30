@php($feature = extendModel($model)->label('Bool On Off Label'))
@php($feature->bool('value')->default(false)->labelsOnOff('This page is active', 'This page is inactive')->help('This is a help message'))

