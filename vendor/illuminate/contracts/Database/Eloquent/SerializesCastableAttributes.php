<?php

namespace EasyCI202402\Illuminate\Contracts\Database\Eloquent;

use EasyCI202402\Illuminate\Database\Eloquent\Model;
interface SerializesCastableAttributes
{
    /**
     * Serialize the attribute when converting the model to an array.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function serialize(Model $model, string $key, $value, array $attributes);
}