<?php

namespace App\Http\Controllers\Concerns;

trait RegistersModulePermissions
{
    protected function registerModulePermissions(string $module): void
    {
        $this->middleware("permission:{$module}.view")->only(['index', 'show']);
        $this->middleware("permission:{$module}.create")->only(['create', 'store']);
        $this->middleware("permission:{$module}.edit")->only(['edit', 'update']);
        $this->middleware("permission:{$module}.delete")->only(['destroy']);
    }
}
