<?php

namespace App\Traits\Models;

use App\Entities\Normalizer\NormalizedNameOptions;
use App\Interfaces\v1\Normalization\NormalizerInterface;

trait WithNormalizedName
{
    public NormalizedNameOptions $options;

    protected static function bootWithNormalizedName(): void
    {
        static::creating(function (self $model) {
            $model->addNormalizedValue();
        });

        static::updating(function (self $model) {
            $model->addNormalizedValue();
        });
    }

    protected function addNormalizedValue(): void
    {
        $options = $this->getOptions();

        $service = app(NormalizerInterface::class);

        $value = $this->{$options->fieldName};

        if ($value) {
            $this->{$options->normalizedFieldName} = $service->normalize($value);
        }
    }

    public function getOptions(): NormalizedNameOptions
    {
        return new NormalizedNameOptions();
    }
}
