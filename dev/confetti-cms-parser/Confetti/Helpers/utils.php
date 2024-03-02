<?php /** @noinspection ALL */

use Confetti\Components\Map;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\Request;

/**
 * @template M
 * @param M $target
 * @return M|\Confetti\Components\Map
 */
function model(\Confetti\Components\Map $target): \Confetti\Components\Map
{
    $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $as = $location['file'] . ':' . $location['line'];

    $contentStore = new ContentStore($target->getComponentKey(), $as);
    $model = $target->newRoot(
        $target->getComponentKey(),
        $as,
    );

    return $model;
}

/**
 * @template M
 * @param M $alias
 * @return M|\Confetti\Components\Map
 */
function query(string $alias): mixed
{
    // Same logic as model()
    return model($alias);
}

/**
 * You can use this in situations where you don't know what the parent classes are.
 */
function extendModel(\Confetti\Helpers\HasMapInterface $component): Map
{
    return $component->toMap();
}

function hashId(string $id): string
{
    return '_' . hash('crc32b', $id);
}

/**
 * We need to define this function here. On the remote server,
 * the blade files are compiled to the cache directory.
 * To handle other none blade files, you can use this
 * function to get the current repository directory.
 */
function repositoryPath(): string
{
    return __REPOSITORY_PATH__;
}

function request(): Request
{
    return new Request();
}

/**
 * @param array $variables exmpale: ['currentContentId', 'The value']
 * Use reference so the blade template doesn't get idea errors 🤫
 */
function variables(&$variables)
{
    // Ignore the keys (present in even indexes)
    $variables = array_values($variables);
    $result = [];
    for ($i = 0; $i < count($variables); $i += 2) {
        $result[] = $variables[$i + 1];
    }
    return $result;
}

function titleByKey(string $key): string
{
    // Guess label from the last part of the relative content id
    $parts = explode('/', $key);
    $part  = end($parts);
    $part  = str_replace('_', ' ', $part);
    return ucwords($part);
}

// This function is used to generate an id for a part of
// a content id. This id is always prefixed with a ~.
// Example: '/model/pages/page~' . newId()
function newId(): string
{
    $char = '123456789ABCDEFGHJKMNPQRSTVWXYZ';
    $encodingLength = strlen($char);
    $desiredLengthTotal = 10;
    $desiredLengthTime = 6;

    // Encode time
    // We use the time since a fixed point in the past.
    // This gives us a more space to use in the feature.
    $time = time() - 1684441872;
    $out = '';
    while (strlen($out) < $desiredLengthTime) {
        $mod = $time % $encodingLength;
        $out = $char[$mod] . $out;
        $time = ($time - $mod) / $encodingLength;
    }

    // Encode random
    while (strlen($out) < $desiredLengthTotal) {
        $rand = random_int(0, $encodingLength - 1);
        $out .= $char[$rand];
    }

    return $out;
}
