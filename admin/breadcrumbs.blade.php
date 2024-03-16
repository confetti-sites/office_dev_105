@php
    use Confetti\Components\Map;
    use Confetti\Helpers\ComponentStandard;
    /**
     * @var string $currentId
     * @var Map[]|ComponentStandard[] $path
     */
    $path = [];
    while ($currentId != null) {
        $currentClass = ComponentStandard::componentClassByContentId($currentId);
        $current = new $currentClass;
        if ($currentId === '/model') {
            break;
        }
        $path[$currentId] = $current;
        $currentId = getParentKey($currentId);
    }
@endphp

<div class="container pt-6">
    @foreach(array_reverse($path) as $currentId => $item)
        <span class="mx-2">/</span>
        <a href="/admin{{ $currentId }}" class="text-blue-500 hover:text-blue-600">{{ $item->getLabel() }}</a>
    @endforeach
</div>