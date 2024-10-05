@php($footer = extendModel($model)->label('Image widthPx'))
@php($footer->image('value')->label('The image label')->widthPx(400)->ratio(400, 300))