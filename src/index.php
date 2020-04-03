<?php 

namespace shehin\procodervalidator;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use shehin\procodervalidator\Constants;
use shehin\procodervalidator\ExcelImport;
use shehin\procodervalidator\Utils;

class ProValidator
{
    private $file;
    public $collection;
    private $invalidColumns;
    private $rowRulesCustomMessage;
    private $validatorBag;
    private $errorBag;
    private $columnLimit;

    public function __construct($file)
    {
        $this->file = $file;
        $this->invalidColumns = collect();
        $this->validatorBag = collect();
        $this->errorBag = collect();    

        $this->excel = new Excel();
        $this->import = new ExcelImport();
        
        $this->excel::import($this->import, $this->file);
        $this->collection = $this->import->getCollection();

        $this->columnLimit = true;
        $this->rowLimit    = true;
    }
    private function throwExceptions(Collection $exceptionBag)
    {
        try {
            $exceptionBag->each(function ($item) {

                switch ($item) {

                    case Constants::EXCEPTION_COLLECTION_NOT_SET :
                        if (!isset($this->collection))
                            throw new \Exception("Collection not set");
                        break;
                    case Constants::EXCEPTION_FILE_NOT_SET :
                        if(!isset($this->file))
                            throw new \Exception("File not set");
                        break;
                    case Constants::EXCEPTION_FILE_NAME_NOT_SET :
                        if(!isset($this->name))
                            throw new \Exception("File name not set");
                        break;
                    case  Constants::EXCEPTION_FILE_EXTENSION_NOT_SET :
                        if(!isset($this->extension))
                            throw new \Exception("File extension not set");
                        break;
                    case Constants::EXCEPTION_COLUMN_LIMIT_NOT_SET :
                        if(!isset($this->columnLimit))
                            throw new \Exception("Column limit not set");
                        break;
                    case  Constants::EXCEPTION_ROW_LIMIT_NOT_SET :
                        if(!isset($this->rowLimit))
                            throw new \Exception("Row limit not set");
                        break;
                    case Constants::EXCEPTION_IMPORT_NOT_SET :
                        if (!isset($this->import))
                            throw new \Exception("Import not set");
                        break;
                    case Constants::EXCEPTION_EXCEL_NOT_SET:
                        if (!isset($this->excel))
                            throw new \Exception("Excel not set");
                        break;
                    case Constants::EXCEPTION_FILE_SHOULD_BE_CSV:
                        if (!in_array($this->extension, $this->allowedExtension))
                            throw new \Exception("File should be a csv");
                        break;
                    case  Constants::EXCEPTION_INVALID_COLUMNS_NOT_SET:
                        if (!isset($this->invalidColumns))
                            throw new \Exception("Invalid column not set");
                        break;
                    case  Constants::EXCEPTION_COLUMNS_NOT_SET:
                        if (!isset($this->requiredColumns))
                            throw new \Exception("Invalid column not set");
                        break;
                    case Constants::EXCEPTION_ROW_RULE_CUSTOM_MESSAGE_NOT_SET:
                        if (!isset($this->rowRulesCustomMessage))
                            throw new \Exception("Row rules custom message");
                        break;
                    case Constants::EXCEPTION_CSV_TERM_MODULE_COLUMNS:
                        if (!isset($this->rowRulesCustomMessage))
                            throw new \Exception("Row rules custom message");
                        break;

                    default :
                        throw new \Exception("Some other exception occured");
                }
            });
        }catch (\Exception $exception)
        {
            throw new \Exception($exception->getMessage());
        }
    }
    private function addToErrorBag($msg)
    {
        try {
            $this->errorBag->push($msg);
            return $this;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    private function getColumnCollection()
    {
        try {
            $this->throwExceptions(
                collect([
                    Constants::EXCEPTION_COLLECTION_NOT_SET
                ])
            );
            return $this->collection->first();
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
    private  function checkEmptyHeaders()
    {
        try {
            $this->throwExceptions(
                collect([
                    Constants::EXCEPTION_COLUMNS_NOT_SET,
                    Constants::EXCEPTION_COLUMN_LIMIT_NOT_SET,
                ])
            );
            if ($this->getColumnCollection()->isEmpty()) {
                $this->addToErrorBag("Header cannot be empty and must contain exactly $this->columnLimit columns [" . Utils::arrayToText($this->requiredColumns->toArray()) . "]");
            }
            return $this;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    private function checkHeaderCount()
    {
        try {
            $this->throwExceptions(
                collect([
                    Constants::EXCEPTION_COLUMNS_NOT_SET,
                    Constants::EXCEPTION_COLUMN_LIMIT_NOT_SET
                ])
            );
            if ($this->getColumnCollection()->count() != $this->columnLimit) {
                $this->addToErrorBag("Headers must contain exactly $this->columnLimit columns [" . Utils::arrayToText($this->requiredColumns->toArray()) . "]");
            }
            return $this;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
    public  function buildInvalidColumnMessage()
    {
        try {
            $this->throwExceptions(
                collect([
                    Constants::EXCEPTION_INVALID_COLUMNS_NOT_SET,
                ])
            );

            if ($this->invalidColumns->isNotEmpty()) {
                $nounType = 'is';
                $columnSuffix = '';

                if ($this->invalidColumns->count() > 1) {
                    $nounType = 'are';
                    $columnSuffix = "'s";
                }
                $this->addToErrorBag("Header column$columnSuffix " . $this->invalidColumns->implode(',') . "$nounType incorrect in csv file");
            }
            return $this;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    private function checkInvalidColumns()
    {
        try{
            $this->getInvalidColumns();
            $this->buildInvalidColumnMessage();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
    private function getInvalidColumns()
    {
        try {
            $this->throwExceptions(
                collect([
                    Constants::EXCEPTION_COLUMNS_NOT_SET,
                    Constants::EXCEPTION_COLUMN_LIMIT_NOT_SET
                ])
            );
            $this->getColumnCollection()->each(function ($item, $count) {
                if (!$this->requiredColumns->contains($item)) {
                    $columnName = empty(trim($item)) ? 'space' : $item;
                    $pos = ++$count;
                    $this->invalidColumns->push('(' . trim($columnName) . ' at ' . $pos . Utils:: ordinalSuffix($pos) . ' column) ');
                }
            });
            return $this;
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    private function setCustomMessage()
    {
        try{
            $this->throwExceptions(
                collect([
                    Constants::EXCEPTION_COLUMN_LIMIT_NOT_SET,
                    Constants::EXCEPTION_ROW_RULE_CUSTOM_MESSAGE_NOT_SET
                ])
            );

            $this->columnLimit--;
            foreach ($this->rowRulesCustomMessage as $key=>$message) {
                foreach (range(0,  $this->columnLimit) as $index) {
                    $this->message[$index.'.'.$key] = ["type"=>$key,"msg"=>Constants::CSV_TERM_MODULE_COLUMNS[$index].''.$message];
                }
            }
            return $this;
        }catch (\Exception $exception)
        {
            throw new \Exception($exception->getMessage());
        }
    }

    private function pushValidatorErrors($errorArray, $index)
    {
        foreach ($errorArray as $error)
        {
            $this->validatorBag->push([
                "row" => $index,
                "type"=>reset($error)["type"],
                "msg"=>reset($error)["msg"]
            ]);
        }
    }

    public function groupValidatorError()
    {

        try {
            $this->validatorBag = $this->validatorBag->mapToGroups(function ($item) {
                return [$item['msg'] => $item['row']];
            });
            return $this;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function moveGroupedValidatorErrorToErrorBag()
    {
        try {
            $this->validatorBag->map(function ($item, $key) {
                $msg = "$key " . Utils::arrayToText($item->toArray());
                $this->addToErrorBag($msg);
                return $msg;
            })->values();
            return $this;
        }catch (\Exception $exception)
        {
            throw new \Exception($exception->getMessage());
        }
    }

    public function buildValidatorError()
    {

        try {
            $this->collection->each(function ($row, $index) {
                $validator = Validator::make(
                    $row->toArray(),
                    $this->rowRules->toArray(),
                    $this->message
                );
                $this->pushValidatorErrors($validator->errors()->toArray(), $index);
            });
            return $this;
        }catch (\Exception $exception){
            throw  new \Exception($exception->getMessage());
        }
    }
    public function validateRows()
    {
        try{
            $this->setCustomMessage()
                ->buildValidatorError()
                ->groupValidatorError()
                ->moveGroupedValidatorErrorToErrorBag();

        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public  function checkRowEmpty()
    {
        try {
            if ($this->collection->isEmpty()) {
                $this->addToErrorBag("Rows cannot be empty");
            }
            return $this;
        }catch (\Exception $exception)
        {
            throw new \Exception($exception->getMessage());
        }
    }

    public function checkRows()
    {
        try{
            $this->checkRowEmpty();
            $this->validateRows();
            return $this;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
    
    public function checkColumns()
    {
        try{
            $this->checkEmptyHeaders()
                ->checkHeaderCount()
                ->checkInvalidColumns();
            return $this;
        }catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
    public function errors()
    {
        try {
            $this->checkColumns()->checkRows();
            return $this->errorBag;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
    public function config($columns,$rules,$messages)
    {
        $this->requiredColumns = $columns;
        $this->rowRules = $rules;
        $this->rowRulesCustomMessage = $messages;

        return $this;
    }
    public function rowLimit($count){
        $this->rowLimit = $count;
    }
    public function columnLimit($count){
        $this->columnLimit = $count;
    }
   
}

