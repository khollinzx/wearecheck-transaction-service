<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;

class  Utils
{
    /**
     * @param int $length
     * @return string
     */
    public function randomPassword(int $length = 8): string
    {
        $reference = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $limit      = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $reference .= $characters[rand(0, $limit)];
        }
        return $reference;
    }

    /**
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function getRecordUsingWhereArrays($query, array $filters): mixed
    {
        #check that the $key exists in the array of $acceptedFilters
        foreach ($filters as $column => $value)
            $query->where($column, '=', $value);
        return $query;
    }

    /**
     * This saves a model records
     *
     * @param Model $model
     * @param array $records
     * @param bool $returnModel
     * @return Model|void
     */
    public static function saveModelRecord(Model $model, array $records = [], bool $returnModel = true)
    {
        if (count($records)) {
            foreach ($records as $k => $v)
                $model->$k = $v;
            $model->save();
        }
        if($returnModel) return $model;
    }

    /**
     * @param Model $model
     * @param Model $polymorphicModel
     * @param string $polymorphicMethod
     * @param array $records
     * @return Model
     */
    public static function savePolymorphicRecord(Model $model, Model $polymorphicModel, string $polymorphicMethod, array $records): Model
    {
        if (count($records)) {
            foreach ($records as $k => $v) {
                $model->$k = $v;
            }
            $polymorphicModel->$polymorphicMethod()->save($model);
        }
        return $model;
    }


    /**
     * @param $query
     * @param array $filters
     * @param array $acceptedFilters
     * @return mixed
     */
    public static function returnFilteredSearchedKeys($query, array $filters, array $acceptedFilters): mixed
    {
        #check that the $key exists in the array of $acceptedFilters
        foreach ($filters as $key => $value)
            if (in_array($key, $acceptedFilters) && $value)
                $query->where($key, 'LIKE', "%$value%");

        return $query;
    }
}

