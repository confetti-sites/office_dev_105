@php
    use Confetti\Helpers\ComponentGenerator;
    use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'hidden',
        decorations: [],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/hidden/component.class.php'),
    ))->toJson();
@endphp
