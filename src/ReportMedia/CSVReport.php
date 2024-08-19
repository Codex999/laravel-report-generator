<?php

namespace Jimmyjs\ReportGenerator\ReportMedia;

use League\Csv\Writer;
use App, Closure, Exception;
use Illuminate\Support\Facades\Storage;
use Jimmyjs\ReportGenerator\ReportGenerator;

class CSVReport extends ReportGenerator
{
    protected $showMeta = false;

    public function download($filename, $save = false)
    {
        if (!class_exists(Writer::class)) {
            throw new Exception(__('laravel-report-generator::exceptions.league_csv_not_found'));
        }

        if ($save) {
            $filePath = $filename;
            $csv = Writer::createFromPath(Storage::path($filePath), 'w');
        } else {
            $csv = Writer::createFromFileObject(new \SplTempFileObject());
        }

        $ctr = 1;

        foreach($this->query_results_data as $result) {
            if(!is_array($result)){
                $data = $result->toArray();
            } else {
                $data = $result;
            }
            if (count($data) > count(array_keys($data))) array_pop($data);
            $csv->insertOne($data);
            $ctr++;
        }

        if (!$save) {
            $csv->output($filename . '.csv');
        }
    }

    public function store($filename)
    {
        $this->download($filename, true);
    }

    private function formatRow($result)
    {
        $rows = [];
        foreach ($this->columns as $colName => $colData) {
            if (is_object($colData) && $colData instanceof Closure) {
                $generatedColData = $colData($result);
            } else {
                $generatedColData = $result->$colData;
            }
            $displayedColValue = $generatedColData;
            if (array_key_exists($colName, $this->editColumns)) {
                if (isset($this->editColumns[$colName]['displayAs'])) {
                    $displayAs = $this->editColumns[$colName]['displayAs'];
                    if (is_object($displayAs) && $displayAs instanceof Closure) {
                        $displayedColValue = $displayAs($result);
                    } elseif (!(is_object($displayAs) && $displayAs instanceof Closure)) {
                        $displayedColValue = $displayAs;
                    }
                }
            }

            array_push($rows, $displayedColValue);
        }

        return $rows;
    }
}
