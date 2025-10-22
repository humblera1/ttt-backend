<?php

namespace App\Interfaces\v1\Resolving;

use App\Enums\Status;
use App\Interfaces\v1\Normalization\NormalizerInterface;
use Illuminate\Database\Eloquent\Builder;

abstract class Resolver
{
    abstract public string $modelClass {
        get;
    }

    protected string $pkFieldName = 'id';

    protected string $statusFieldName = 'status';

    protected string $normalizedFieldName = 'normalized_name';

    protected string $rawValueFieldName = 'name';

    protected string $defaultStatusValue = Status::Pending->value;

    public function __construct
    (
        protected NormalizerInterface $normalizer,
    )
    {}

    public function resolveMany(array $names): array
    {
        $normalizedMap = $this->getNormalizedMap($names);

        if (empty($normalizedMap)) {
            return [];
        }

        $existingMap = $this->getExistingMap(array_keys($normalizedMap));

        $ids = [];
        $toCreate = [];

        foreach ($normalizedMap as $normalized => $raw) {
            if (isset($existingMap[$normalized])) {
                $ids[] = $existingMap[$normalized];

                continue;
            }

            $toCreate[] = $raw;
        }

        foreach ($toCreate as $item) {
            $created = $this->modelClass::query()
                ->create([
                    $this->rawValueFieldName => $item,
                    $this->statusFieldName => $this->defaultStatusValue,
                ]);

            $ids[] = $created->{$this->pkFieldName};
        }

        return $ids;
    }

    protected function getQuery(array $normalized): Builder
    {
        return $this->modelClass::query()
            ->whereIn($this->normalizedFieldName, $normalized);
    }

    protected function getNormalizedMap(array $rawNames): array
    {
        $normalizedMap = [];

        foreach ($rawNames as $name) {
            $normalizedName = $this->normalizer->normalize($name);

            if (empty($normalizedName)) {
                continue;
            }

            $normalizedMap[$normalizedName] = $name;
        }

        return $normalizedMap;
    }

    protected function getExistingMap(array $normalizedValues): array
    {
        $existing = $this->getQuery($normalizedValues)
            ->get([
                $this->pkFieldName,
                $this->normalizedFieldName,
            ]);

        $existingMap = [];

        foreach ($existing as $model) {
            $existingMap[$model->{$this->normalizedFieldName}] = $model->{$this->pkFieldName};
        }

        return $existingMap;
    }
}
