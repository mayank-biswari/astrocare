<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PermissionGroupingService
{
    /**
     * Group permissions by their prefix (text before first space).
     * Permissions without a space go into "General" group.
     *
     * @param Collection $permissions
     * @return Collection keyed by group name, values are permission collections
     */
    public static function group(Collection $permissions): Collection
    {
        $grouped = $permissions->groupBy(function ($permission) {
            $name = \is_string($permission) ? $permission : $permission->name;
            $spacePos = strpos($name, ' ');

            if ($spacePos === false) {
                return 'General';
            }

            return ucfirst(substr($name, 0, $spacePos));
        });

        return $grouped->sortKeys()->pipe(function ($collection) {
            if ($collection->has('General')) {
                $general = $collection->get('General');
                $collection->forget('General');
                $collection->put('General', $general);
            }

            return $collection;
        });
    }
}
