<?php
/**
 * Created by PhpStorm.
 * User: shehin
 * Date: 16/10/19
 * Time: 1:16 PM
 */

namespace shehin\procodervalidator;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelImport implements ToCollection
{


    private $collection;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        if(!isset($this->collection))
            throw new \Exception("Collection is not set");
       return $this->collection;
    }
}