<?php

namespace Confetti\Helpers;

use Confetti\Components\Map;

class QueryBuilder
{
    private const DEFAULT_OPTIONS = ['response_with_full_id' => false];
    private array $queryStack = [];
    private array $query;

    public function __construct(string $from, string $as = null)
    {
        $this->newQuery($from, $as, self::DEFAULT_OPTIONS);
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @throws \JsonException
     */
    public function run(): array
    {
        $client   = new Client();
        $response = $client->get('confetti-cms-content/contents', [
            'accept' => 'application/json',
        ], $this->getFullQuery());
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string[] $options
     */
    public function setOptions(array $options): self
    {
        $this->query['options'] = array_merge(self::DEFAULT_OPTIONS, $options);
        return $this;
    }

    /**
     * @param string[] $select
     */
    public function setSelect(array $select): self
    {
        $this->query['select'] = $select;
        return $this;
    }

    public function wrapJoin(string $from, string $as = null): self
    {
        // Copy query so the rest of the page has the old query
        $child = $this;
        // We don't want to select anything from the parent
        $child->query['select'] = [];
        $child->queryStack[] = $child->query;
        $child->newQuery($from, $as);
        return $child;
    }

    public function ignoreFirstRow(): void
    {
        // Ignore first row of this leven
        $limit = $this->getLimit();
        if ($limit !== null) {
            $this->setLimit($limit - 1);
        }
        $offset = $this->getOffset();
        $this->setOffset($offset + 1);
        // We only want to fetch one row for the parent queries
//        $queryStack = [];
//        foreach ($this->queryStack as $parent) {
//            $parent['limit'] = 1;
//            $queryStack[] = $parent;
//        }
//        $this->queryStack = $queryStack;
    }

    public function appendWhere(string $key, string $operator, mixed $value): self
    {
        if ($value instanceof ComponentInterface) {
            $this->query['where'][] = [
                'field'          => $key,
                'operator'       => $operator,
                'expression_key' => $value,
            ];
        } else {
            $this->query['where'][] = [
                'field'            => $key,
                'operator'         => $operator,
                'expression_value' => $value,
            ];
        }

        return $this;
    }

    public function appendOrderBy(string $key, string $direction = 'ascending'): self
    {
        $this->query['order_by'][] = [
            'key'       => $key,
            'direction' => $direction,
        ];

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->query['limit'] ?? null;
    }

    public function setLimit(int $limit): self
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    public function getOffset(): int
    {
        return $this->query['offset'] ?? 0;
    }

    public function setOffset(int $offset): self
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    private function newQuery(string $from, ?string $as, array $options = [])
    {
        $this->query = [];
        if (!empty($options)) {
            $this->query['options'] = $options;
        }
        $this->query['from'] = $from;
        if ($as) {
            $this->query['as'] = $as;
        }
    }

    private function getFullQuery(): array
    {
        $result = $this->query;
        $options = $result['options'] ?? throw new \RuntimeException('No options set');
        unset($result['options']);
        foreach (array_reverse($this->queryStack) as $parent) {
            $parent['join'] = [$result];
            $result         = $parent;
        }
        // Only the root query should have the options
        $result['options'] = $options;
        return $result;
    }
}