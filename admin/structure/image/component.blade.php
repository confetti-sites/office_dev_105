@php
    use Confetti\Helpers\ComponentGenerator;
    use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'image',
        decorations: [
            Decoration::LABEL->comment('Label is used as a field title in the admin panel'),
            Decoration::HEIGHT->comment('Height of the image'),
            Decoration::WIDTH->comment('Width of the image'),
            Decoration::RATIO->comment('Popular ratios are 16:9, 4:3, 1:1'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/image/component.class.php'),
    ))->toJson();
@endphp
