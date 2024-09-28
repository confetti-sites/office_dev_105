@php($footer = extendModel($model)->label('Color basic'))
@php($footer->color('value')->label('The label')->help('This is a help text for the color field.')->default('#ff0000'))
