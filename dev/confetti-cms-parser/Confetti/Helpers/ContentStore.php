<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ContentStore
{
    private QueryBuilder $queryBuilder;
    private array $content = [];
    private bool $alreadyInit = false;

    public function __construct(string $from, string $as)
    {
        $this->queryBuilder = new QueryBuilder($from, $as);
    }

    public function init(): void
    {
        if ($this->alreadyInit) {
            return;
        }
        $this->content = $this->queryBuilder->get();
        $this->alreadyInit = true;
    }

    public function find(string $id): mixed
    {
        $this->queryBuilder->select([$id]);
        $result = $this->queryBuilder->get();
        if (count($result) === 0) {
            return null;
        }
        return $result[0]['data'][$id] ?? null;
    }

    public function findMany(string $id): array
    {
        exit('todo');
        $this->content = $this->queryBuilder->get();
        return $this->content["data"];
    }
}
