<?php

namespace SwissKnife202409\Illuminate\Contracts\Database\Eloquent;

use SwissKnife202409\Illuminate\Database\Eloquent\Model;
interface CastsInboundAttributes
{
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, $value, array $attributes);
}
