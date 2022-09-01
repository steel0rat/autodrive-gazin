<?php

namespace App\Http\Controllers\EntityUpdater;

interface EntityUpdaterInterface
{
    public function updateEntities($newModelsArr):array;
}
