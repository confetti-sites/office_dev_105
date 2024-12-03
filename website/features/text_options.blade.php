@php($feature = extendModel($model)->label('Text basic'))
@php($feature->text('value')->label('Text')->min(3)->max(20)->default('Confetti CMS')->placeholder('Enter text here')->help('This is a help text.'))
