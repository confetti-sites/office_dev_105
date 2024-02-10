<?php

namespace Confetti\Helpers;

use Confetti\Components\Map;

class QueryBuilder
{
    private const DEFAULT_OPTIONS = ['response_with_full_id' => true];
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
    public function get(): array
    {
        $client   = new Client();
        $response = $client->get('confetti-cms-content/contents', [
            'accept' => 'application/json',
        ], $this->query);
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

    public function join(string $from, string $as = null): self
    {
        $this->queryStack[] = $this->query;
        $this->newQuery($from, $as);
        return $this;
    }

    public function where(string $key, string $operator, mixed $value): self
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

    public function orderBy(string $key, string $direction = 'ascending'): self
    {
        $this->query['order_by'][] = [
            'key'       => $key,
            'direction' => $direction,
        ];

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    public function offset(int $offset): self
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
}