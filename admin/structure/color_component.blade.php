@php
    use Confetti\Helpers\ComponentGenerator;
    use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'color',
        decorations: [
            Decoration::DEFAULT->comment('If no value is given, this will be used'),
            Decoration::LABEL->comment('Label is used as a field title in the admin panel'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/color_component.class.php'),
    ))->toJson();
@endphp
