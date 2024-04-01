@php
    use Confetti\Helpers\ComponentGenerator;use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'select',
        decorations: [
            Decoration::DEFAULT->comment('Before saving this will be the default.'),
            Decoration::LABEL->comment('Label is used as a field title in the admin panel'),
            Decoration::OPTIONS->comment('List of options. For now, only values are supported.'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/select/component.class.php'),
    ))->toJson();
@endphp
