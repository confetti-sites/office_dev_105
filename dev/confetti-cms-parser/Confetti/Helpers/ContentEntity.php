<?php

declare(strict_types=1);

namespace Confetti\Helpers;


class ContentEntity
{
    public string $contentId;
    public string $value;

    public function __toString(): string
    {
        return $this->value;
    }

    public static function byDbRow(array $row): self
    {
        $entity = new self();
        $entity->contentId = $row['id'];
        $entity->value = $row['value'];
        return $entity;
    }

    /**
     * @return ContentEntity[]
     */
    public static function byDbRows(array $rows): array
    {
        $entities = [];
        foreach ($rows as $row) {
            $entities[] = self::byDbRow($row);
        }
        return $entities;
    }
}
