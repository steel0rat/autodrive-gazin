<?php

namespace App\Http\Controllers\EntityUpdater;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EntityUpdater\EntityUpdaterInterface;
use Illuminate\Support\Facades\DB;

class EntityUpdater extends Controller implements EntityUpdaterInterface
{

    public $workingClass;
    public $primaryKeys;
    public $indexKeys;

    public function __construct($workingClass, $primaryKeys, $indexKeys = [])
    {
        $this->workingClass = $workingClass;
        $this->primaryKeys = $primaryKeys;
        $this->indexKeys = $indexKeys;
    }

    public function updateEntities($newModelsArr, $insertFlag = true, $updateFlag = true, $deleteFlag = true):array
    {
        $dbEntityIndexes = $this->workingClass::all($this->primaryKeys)->toArray();

        $newEntityIndexes = [];
        foreach ($newModelsArr as $keyEl => $el) {
            $newModelElArr = [];
            foreach ($this->primaryKeys as $pk) {
                $newModelElArr[$pk] = $el->$pk;
            }
            $newEntityIndexes[$keyEl] = $newModelElArr;
        }

        $returnInfoArr = [];
        if ($deleteFlag) {
            $entityToDelete   = $this->getEntityToDelete($dbEntityIndexes, $newEntityIndexes);
            if(count($entityToDelete)){
                $entityToDeleteDb = $this->getFromDbByPk($entityToDelete);
                foreach ($entityToDeleteDb as $obj) {
                    if ($this->validateToDoAction($obj, $entityToDelete)){
                        $obj->delete();
                    }
                }
                $returnInfoArr['deleted'] = $entityToDeleteDb->count();
                unset($entityToDeleteDb);
            }
            unset($entityToDelete);
        }

        if ($updateFlag) {
            $entityToUpdate = $this->getEntityToUpdate($dbEntityIndexes, $newEntityIndexes);
            $entityToUpdateDb = $this->getFromDbByPk($entityToUpdate);
            $updCount = 0;
            foreach ($entityToUpdateDb as $obj) {
                $newVersion = $this->findToUpdate($obj, $newModelsArr);
                if ($newVersion) {
                    foreach ($this->indexKeys as $key) {
                        $obj->$key = $newVersion->$key;
                    }
                    $obj->save();
                    $updCount++;
                }
            }
            if ($updCount){
                $returnInfoArr['updated'] = $updCount;
            }
            unset($entityToUpdate);
            unset($entityToUpdateDb);
        }

        if ($insertFlag) {
            $entityToInsert = $this->getEntityToInsert($dbEntityIndexes, $newEntityIndexes);
            if(count($entityToInsert)){
                foreach ($newModelsArr as $obj) {
                    if ($this->validateToDoAction($obj, $entityToInsert)){
                        $obj->save();
                    }
                }
                $returnInfoArr['isnerted'] = count($entityToInsert);
            }
            unset($entityToInsert);
        }

        return $returnInfoArr;
    }

    private function findToUpdate($object, $newModels)
    {
        foreach ($newModels as $newVer) {
            foreach ($this->primaryKeys as $key) {
                if ($object->$key !== $newVer->$key) {
                    continue(2);
                }
            }

            foreach ($this->indexKeys as $key) {
                if ($object->$key !== $newVer->$key) {
                    return $newVer;
                }
            }
        }
        return False;
    }

    private function validateToDoAction($object, &$validKeys):bool
    {
        foreach ($validKeys as $composite) {
            foreach ($composite as $key => $val) {
                if ($object->$key !== $val) {
                    continue(2);
                }
            }
            return True;
        }
        return False;
    }

    private function getFromDbByPk($entityKeys)
    {

        $whereInArr = [];
        foreach ($entityKeys as $item) {
            foreach ($item as $key => $el) {
                $whereInArr[$key][] = $el;
            }
        }

        $dbQuerry = new $this->workingClass;
        foreach ($whereInArr as $key => $arr) {
            $dbQuerry = $dbQuerry->whereIn($key, $arr);
        }

        return $dbQuerry->get();
    }

    private function getEntityToInsert($old, $new):array
    {

        foreach ($new as $newKey => $newEl) {
            foreach ($old as $oldEl){
                foreach ($this->primaryKeys as $key) {
                    if ($oldEl[$key] !== $newEl[$key]) {
                        continue(2);
                    }
                }
                unset($new[$newKey]);
                continue(2);
            }
        }
        return $new;
    }

    private  function getEntityToUpdate($old, $new):array
    {
        $arr = [];
        foreach ($old as $oldEl) {
            foreach ($new as $keyNewEl => $newEl){
                foreach ($this->primaryKeys as $key) {
                    if ($oldEl[$key] !== $newEl[$key]) {
                        continue(2);
                    }
                }
                $arr[$keyNewEl] = $newEl;
                continue(2);
            }
        }
        return $arr;
    }

    private  function getEntityToDelete($old, $new):array
    {
        foreach ($old as $oldKey => $oldEl) {
            foreach ($new as $newEl){
                foreach ($this->primaryKeys as $key) {
                    if ($oldEl[$key] !== $newEl[$key]) {
                        continue(2);
                    }
                }
                unset($old[$oldKey]);
                continue(2);
            }
        }
        return $old;
    }

}
