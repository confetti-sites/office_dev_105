<?php

namespace Confetti\Helpers;

use Confetti\Components\Map;

class QueryBuilder
{
    private const MODEL_PREFIX = '/model/';

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

    public function replaceFrom(string $relativeId): void
    {
        $this->query['from'] = $relativeId;
    }

    /**
     * @throws \JsonException
     */
    public function run(): array
    {
        $client   = new Client();

        // Use static to exit when second time called
        static $nr = 0;
        $nr++;

        $response = $client->get('confetti-cms-content/contents', [
            'accept' => 'application/json',
        ], $this->getFullQuery());

        if ($nr === 10000) {
            throw new \RuntimeException('Too many database requests (10000)');
        }

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

    public function wrapJoin(string $parentFrom, string $from, string $as = null): void
    {
        // We don't want to select anything from the parent
        $this->query['select'] = [];
        $this->query['from']   = $parentFrom;
        $this->queryStack[]    = $this->query;
        $this->newQuery($from, $as);
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
    }

    public function appendWhere(string $key, string $operator, mixed $value): self
    {
        if ($value !== null && str_starts_with($value, self::MODEL_PREFIX)) {
            $this->query['where'][] = [
                'key'            => $key,
                'operator'       => $operator,
                'expression_key' => $value,
            ];
            return $this;
        }

        $this->query['where'][] = [
            'key'              => $key,
            'operator'         => $operator,
            'expression_value' => $value,
        ];

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

    private function getFullQuery(): array
    {
        $result  = $this->query;
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

    /**
     * This function returns the current condition of the query
     * With this condition, we can check if the desired condition
     * is met with the condition of the already retrieved content.
     */
    public function getCurrentCondition(): string
    {
        $result = "";
        foreach ($this->query['where'] ?? [] as $i => $where) {
            $prefix = $i == 0 ? 'where' : 'and';
            $expression = $where['expression_key'] ?? '';
            if ($expression === '') {
                $expression = $where['expression_value'] ?? 'null';
            }
            $result .= sprintf(" %s %s %s %s", $prefix, $where['key'], $where['operator'], $expression);
        }
        foreach ($this->query['order_by'] ?? [] as $i => $orderBy) {
            $prefix = $i == 0 ? ' order_by' : ',';
            $result .= "{$prefix} {$orderBy['key']} {$orderBy['direction']}";
        }
        if (($this->query['limit'] ?? 0) > 0) {
            $result .= " limit {$this->query['limit']}";
        }
        if (($this->query['offset'] ?? 0) > 0) {
            $result .= " offset {$this->query['offset']}";
        }
        return ltrim($result, ' ');
    }

    private function newQuery(string $from, ?string $as, array $options = []): void
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
}