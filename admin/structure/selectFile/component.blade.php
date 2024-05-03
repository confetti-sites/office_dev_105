@php
    use Confetti\Helpers\ComponentGenerator;use Confetti\Helpers\Decoration;
    echo(new ComponentGenerator(
        name: 'selectFile',
        decorations: [
            Decoration::MATCH->comment(
                '
                List all files by directories. You can use the glob pattern. For example: `->match([\'/view/footers\'])`

                @param string $pattern A glob pattern.

                The ? matches 1 of any character except a /
                The * matches 0 or more of any character except a /
                The [abc] matches 1 of any character in the set
                The [!abc] matches 1 of any character not in the set
                The [a-z] matches 1 of any character in the range
	            character class (must be non-empty)

                Examples:
                    `/*/*.css` to match /templates/style.css
                    `[\'/*.css\',\'/*/*.css\']` to match /style.css and /templates/style.css
                '),
            Decoration::DEFAULT->comment('Before saving this will be the default file. With match, the file must be in the directory.'),
            Decoration::LABEL->comment('Label is used as a field title in the admin panel'),
            Decoration::USE_LABEL_FOR->comment('Use the label of the selected option to fill the given field'),
        ],
        phpClass: file_get_contents(repositoryPath() . '/admin/structure/selectFile/component.class.php'),
    ))->toJson();
@endphp
