<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;

use App\Models\Car as CarDbModel;
use App\Http\Controllers\CarController;
use App\Models\CarMark;
use App\Models\CarModel;
use App\Models\CarGeneration;
use App\Models\CarColor;
use App\Models\CarBodyType;
use App\Models\CarEngineType;
use App\Models\CarTransmitionType;
use App\Models\CarGearType;
use Illuminate\Support\Facades\DB;

class Car extends Command
{
    protected $signature = 'car:import {pathToFile=storage/app/car/data.xml}';
    protected $description = 'Import cars from data file';

    //for xml validate
    protected $xmlAttrs = [
        'id'            => 'intval',
        'generation_id' => 'intval',
        'mark'          => 'strval',
        'model'         => 'strval',
        'generation'    => 'strval',
        'year'          => 'intval',
        'run'           => 'intval',
        'color'         => 'strval',
        'body-type'     => 'strval',
        'engine-type'   => 'strval',
        'transmission'  => 'strval',
        'gear-type'     => 'strval',
    ];

    //path to file
    protected $file;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->file = base_path($this->argument('pathToFile'));

        $this->info("Step 1: Load file");
        //try to load file
        try {
            $fileContent = file_get_contents($this->file);
        } catch (Exception $e) {
            $this->error("File ($this->file) does not exist");
            return 0;
        }

        //try to load xml
        try {
            $xmlFile = simplexml_load_string($fileContent);
            unset($fileContent);
        } catch (Exception $e) {
            $this->error("File ($this->file) is not xml");
            return 0;
        }

        //check offers in xml
        if (!$this->checkOffersInFile($xmlFile)) {
            $this->error("Offers does not exist in ($this->file)");
            return 0;
        }

        //transform to array
        $jsonRaw = json_encode($xmlFile->offers);
        $fileCarsArrUnvalidated = json_decode($jsonRaw, true)['offer'];
        unset($jsonRaw);

        $this->info("Step 2: Validate file offers");
        $fileCarsArr = $this->validateOffers($fileCarsArrUnvalidated);
        unset($fileCarsArrUnvalidated);

        $countOffers = count($fileCarsArr);
        if($countOffers === 0) {
            $this->error("No one valid offer in file");
            return 0;
        }

        $this->infoAboutOffers($countOffers);

        DB::beginTransaction();
            $this->info("Step 3: Load taxonomies from file");
            $taxonomyFromFile = $this->getTaxonomies($fileCarsArr);

            $this->info("Step 4: Generating models");
            $modelsFromFile = $this->generateModels($fileCarsArr, $taxonomyFromFile);
            unset($fileCarsArr, $taxonomyFromFile);

            $this->info("Step 5: Insert, update and delete car offers");
            $carController = new CarController();
            $resultArr = $carController->updateCarTable($modelsFromFile);

        DB::commit();

        if(count($resultArr)){
            $this->table(
                [array_keys($resultArr)],
                [$resultArr]
            );
        } else {
            $this->error('No changes was load. No new info about offers');
        }


        return 0;
    }

    protected function checkOffersInFile($xml):bool
    {
        if (isset($xml->offers->offer)) {
            return count($xml->offers->offer) > 0;
        }
        return False;
    }

    protected function validateOffers($carArrFromFile):array
    {

        $badOffers   = [];
        $fileCarsArr = [];
        foreach ($carArrFromFile as $key => $el) {
            $offer = [];
            foreach ($this->xmlAttrs as $attrKey => $attrType) {
                if (!is_array($el[$attrKey])) {
                    $val = $attrType($el[$attrKey]);
                    $offer[$attrKey] = $val;
                    continue;
                }
                break;
            }
            if (count($offer) === count($this->xmlAttrs)) {
                $fileCarsArr[] = $offer;
                continue;
            }
            $badOffers[$key] = $attrKey;
        }

        $this->warnBadOffers($badOffers);
        unset($badOffers);
        return  $fileCarsArr;
    }


    protected function getTaxonomies(&$fileCarsArr):array
    {
        $taxMarks = [];
        $taxonomies = [];
        foreach ($fileCarsArr as $el) {
            $taxMarks[$el['mark']][$el['model']][$el['generation']] = null;
            $taxonomies[CarColor::class][$el['color']] = null;
            $taxonomies[CarEngineType::class][$el['engine-type']] = null;
            $taxonomies[CarTransmitionType::class][$el['transmission']] = null;
            $taxonomies[CarGearType::class][$el['gear-type']] = null;
            $taxonomies[CarBodyType::class][$el['body-type']] = null;
        }

        foreach ($taxonomies as $model => &$vals) {
            foreach ($vals as $key => &$val) {
                $val = $model::firstOrCreate(['caption' => $key]);
            }
        }

        foreach ($taxMarks as $markKey => $marks) {
            $mark = CarMark::firstOrCreate(['caption' => $markKey]);
            $taxonomies[CarMark::class][$markKey]['model'] = $mark;
            foreach ($marks as $modelKey => $models) {
                $model = CarModel::firstOrCreate(['caption'=>$modelKey, 'mark_id'=>$mark->id]);
                $taxonomies[CarMark::class][$markKey][CarModel::class][$modelKey]['model'] = $model;
                foreach ($models as $generationKey => $generation) {
                    $generation = CarGeneration::firstOrCreate(['caption'=>$modelKey, 'mark_id'=>$mark->id, 'model_id'=>$model->id]);
                    $taxonomies[CarMark::class][$markKey][CarModel::class][$modelKey][CarGeneration::class][$generationKey]['model'] = $generation;
                }
            }
        }
        return $taxonomies;
    }

    protected function generateModels(&$fileCarsArr, &$taxonomies):array
    {

        $modelsFromFile = [];
        foreach ($fileCarsArr as $el) {
            $modelsFromFile[] = new CarDbModel([
                'car_id'               => $el['id'],
                'generation_id'        => $el['generation_id'],
                'year'                 => $el['year'],
                'run'                  => $el['run'],
                'mark_id'              => $taxonomies[CarMark::class][$el['mark']]['model']->id,
                'model_id'             => $taxonomies[CarMark::class][$el['mark']][CarModel::class][$el['model']]['model']->id,
                'model_generation_id'  => $taxonomies[CarMark::class][$el['mark']][CarModel::class][$el['model']][CarGeneration::class][$el['generation']]['model']->id,
                'color_id'             => $taxonomies[CarColor::class][$el['color']]->id,
                'engine_type_id'       => $taxonomies[CarEngineType::class][$el['engine-type']]->id,
                'transmition_type_id'  => $taxonomies[CarTransmitionType::class][$el['transmission']]->id,
                'gear_type_id'         => $taxonomies[CarGearType::class][$el['gear-type']]->id,
                'body_type_id'         => $taxonomies[CarBodyType::class][$el['body-type']]->id,
            ]);
        }
        return $modelsFromFile;
    }

    // functions for pretty console output, not exactly necessary

    protected $prettyWarnLenght = 0;

    protected function warnBadOffers($badOffers):void
    {
        if(count($badOffers)) {
            $space = mb_strlen(max(array_keys($badOffers))) +1;
            $secondSpace = mb_strlen(max($badOffers)) + 1;
            $this->warn(" ╭" . str_repeat("━", 27 + $secondSpace + $space). "╮");
            foreach ($badOffers as $key => $el) {
                $this->warn(
                    " │ Bad offer: index($key)" .
                    str_repeat(" ", $space-mb_strlen($key)) .
                    "reason: $el" .
                    str_repeat(" ", $secondSpace - mb_strlen($el)) .
                    "│"
                );
            }
            $this->warn(" ├" . str_repeat("┄", 27 + $secondSpace + $space). "┤");
            $this->warn(" │ Found " . count($badOffers) . " bad offers" . str_repeat(" ", $space + $secondSpace + 8) . "│");
            $this->warn(" ╰" . str_repeat("━", 27 + $secondSpace + $space). "╯");
            $this->prettyWarnLenght = 30 + $space + $secondSpace;
        }
    }

    protected function infoAboutOffers($count):void
    {
        $len = mb_strlen($count);
        $leftSpace = ($this->prettyWarnLenght) ? round(($this->prettyWarnLenght - 31 - $len)/2, 0) : 1;

        $this->info(str_repeat(" ", $leftSpace) . "╭" . str_repeat("─", 29 + $len) . "╮");
        $this->info(str_repeat(" ", $leftSpace) . "│ Found $count valid offers in file │");
        $this->info(str_repeat(" ", $leftSpace) . "╰" . str_repeat("─", 29 + $len) . "╯");
    }

    protected function infoAboutActions($type, $count):void
    {
        switch ($type) {
            case 'isnerted':
                $color = 'info';
                break;
            case 'updated':
                $color = 'question';
                break;
            case 'deleted':
                $color = 'error';
                break;
        }


        $len = mb_strlen($count);
        $leftSpace = ($this->prettyWarnLenght) ? round(($this->prettyWarnLenght - 31 - $len)/2, 0) : 1;

        $this->$color(str_repeat(" ", $leftSpace) . "╭" . str_repeat("─", 29 + $len) . "╮");
        $this->$color(str_repeat(" ", $leftSpace) . "│ Found $count valid offers in file │");
        $this->$color(str_repeat(" ", $leftSpace) . "╰" . str_repeat("─", 29 + $len) . "╯");
    }
}
