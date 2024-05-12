@php
    use Confetti\Helpers\ComponentGenerator;
    use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'image',
        decorations: [
            Decoration::LABEL->comment('Label is used as a field title in the admin panel'),
            Decoration::WIDTH_PX->comment('Width of the image in pixels. Automatically smaller for mobile devices and 2x higher for retina displays'),
            Decoration::RATIO->comment('Popular ratios are 16:9, 4:3, 1:1'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/image/component.class.php'),
    ))->toJson();
@endphp
