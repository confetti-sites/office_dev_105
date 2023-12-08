@php
    use Confetti\Helpers\ComponentGenerator;
    use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'list',
        decorations: [
            Decoration::MIN->comment('Minimum number of items'),
            Decoration::MAX->comment('Maximum number of items'),
            Decoration::COLUMNS->comment('This becomes the headers of the table in de admin'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/list_component.class.php'),

    ))->toJson();
@endphp
