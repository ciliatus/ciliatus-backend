<?php

namespace App\Ciliatus\Api\Http\Transformers;

use App\Ciliatus\Common\Models\Model;

interface TransformerInterface
{

    public function transform(Model $model): array;

}
