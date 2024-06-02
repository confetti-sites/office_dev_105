@php($m = extendModel($model)->label('Divider'))
<div style="background-color: {{ $m->select('color')->options(['red', 'green'])->default('red')->required() }}; height: 2px; width: 100%;"></div>
