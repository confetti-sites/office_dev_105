<?php

declare(strict_types=1);

namespace Confetti\Helpers;

use JsonException;

class ContentStore
{
    public function find(string $id): ?ContentEntity
    {
        $contents = $this->findMany($id);
        if (count($contents) === 0) {
            return null;
        }
        return $contents[0];
    }

    /**
     * @return ContentEntity[]
     */
    public function findMany(string $id): array
    {
        $client   = new Client();
        $response = $client->get('confetti-cms-content/contents?id=' . $id, [
            'accept' => 'application/vnd.confetti.seperated+json',
        ]);
        try {
            $contents = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        return ContentEntity::byDbRows($contents['data']);
    }

    /**
     * @return ContentEntity[]
     */
    public function whereIn(string $parentId, array $relativeIds, bool $includePrefixAsId = false): array
    {
        $client   = new Client();
        $query    = http_build_query([
            'id_prefix'            => $parentId,
            'id'                   => implode(',', $relativeIds),
            'include_prefix_as_id' => $includePrefixAsId ? 'true' : 'false',
        ]);
        $response = $client->get('confetti-cms-content/contents?' . $query, [
            'accept' => 'application/vnd.confetti.seperated+json',
        ]);
        try {
            $contents = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        return ContentEntity::byDbRows($contents['data']);
    }
}
