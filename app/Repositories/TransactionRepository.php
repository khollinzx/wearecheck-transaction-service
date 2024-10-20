<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\Country;
use App\Models\Currency;
use App\Models\State;
use App\Models\Transaction;
use App\Models\UnicoopAdmin;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TransactionRepository extends BaseRepositoryAbstract
{

    /**
     * @var string
     */
    protected string $databaseTableName = 'transactions';

    /**
     *
     * @param Transaction $model
     */
    public function __construct(Transaction $model)
    {
        parent::__construct($model, $this->databaseTableName);
    }

    /**
     * @param array $queries
     * @return mixed
     */
    public function findByWhere(array $queries)
    {
        return $this->model::with($this->model->relationships)->where($queries)->sharedLock()->first();
    }

    /**
     * @param string $column
     * @param $value
     * @param array $queries
     * @return mixed
     */
    public function findByWhereNot(string $column, $value, array $queries)
    {
        return $this->model::with($this->model->relationships)->where($column, "!=", $value)->where($queries)->sharedLock()->first();
    }

    /**
     * @param array $queries
     * @return mixed
     */
    public function getByWhere(array $queries)
    {
        return $this->model::with($this->model->relationships)->where($queries)->sharedLock()->get();
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->model::with($this->model->relationships)->sharedLock()->orderByDesc('id')->get();
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        try {
            $data = [];
            $records = $this->model::sharedLock()->orderByDesc('id')->get();;
            if(count($records))
                collect($records)->each( function ($record) use (&$data) {
                    $data[] = ($record);
                });
            return $data;
        } catch (\Exception $exception) {
            Log::error($exception);
            return [];
        }
    }

}
