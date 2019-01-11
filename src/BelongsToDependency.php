<?php

namespace Manmohanjit\BelongsToDependency;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class BelongsToDependency extends BelongsTo
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'belongs-to-dependency';

    /**
     * Resolve the field's value.
     * This overrides the default function to fix the eager-loading issue:
     * https://github.com/laravel/nova-issues/issues/246#issuecomment-452390026
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        if (array_key_exists($this->attribute, $resource->getRelations()) && $resource->relationLoaded($this->attribute)) {
            $value = $resource->{$this->attribute};
        } else {
            $value = $resource->{$this->attribute}()->withoutGlobalScopes()->setEagerLoads([])->first();
        }

        if ($value) {
            $this->belongsToId = $value->getKey();

            $this->value = $this->formatDisplayValue($value);
        }
    }


    /**
     * Build an associatable query for the field.
     * Here is where we add the depends on value and filter results
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAssociatableQuery(NovaRequest $request, $withTrashed = false)
    {
        $query = parent::buildAssociatableQuery($request, $withTrashed);

        if($request->has('dependsOnValue')) {
            $query->where($this->meta['dependsOnKey'], $request->dependsOnValue);
        }

        return $query;
    }

    /**
     * Set the depends on field and depends on key
     *
     * @param  string $dependsOnField
     * @param  string $tableKey
     * @return $this
     */
    public function dependsOn($dependsOnField, $tableKey)
    {
        return $this->withMeta([
            'dependsOn' => $dependsOnField,
            'dependsOnKey' => $tableKey,
        ]);
    }
}
