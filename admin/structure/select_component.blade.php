@php
    use Confetti\Helpers\ComponentGenerator;use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'select',
        decorations: [
            Decoration::BY_DIRECTORY
            ->comment(
                '
                List all files by directories. You can use the glob pattern. For example: `->fileInDirectories([\'/view/footers\'])`

                @param string $pattern A glob pattern.

                The ? matches 1 of any character except a /
                The * matches 0 or more of any character except a /
                The ** matches 0 or more of any character including a /
                The [abc] matches 1 of any character in the set
                The [!abc] matches 1 of any character not in the set
                The [a-z] matches 1 of any character in the range

                Examples: *.css /templates/**.css'),
            Decoration::DEFAULT->comment('Before saving this will be the default file. When fileInDirectories is provided, the file must be in the directory.'),
            Decoration::LABEL->comment('Label is used as a field title in the admin panel'),
            Decoration::OPTIONS->comment('List of options. For now, only values are supported.'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/select_component.class.php'),
    ))->toJson();
@endphp
