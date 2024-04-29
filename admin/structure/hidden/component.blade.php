@php
    use Confetti\Helpers\ComponentGenerator;
    use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'hidden',
        decorations: [
            Decoration::LABEL->comment('Labels are displayed in other parts of the admin panel, like in a list.'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/hidden/component.class.php'),
    ))->toJson();
@endphp
