<?php

namespace LaravelCake\Lead\Repositories;

use LaravelCake\Lead\Repositories\BaseRepository;
use Exception;
use LaravelCake\Lead\Models\ContactInfutorSummary;

/**
 * Class ContactInfutorSummaryRepository
 *
 * @package LaravelCake\Lead\Repositories
 */
class ContactInfutorSummaryRepository extends BaseRepository
{
    /**
     * __construct
     *
     * @param  ContactInfutorSummaryRepository  $model
     * @return void
     */

    public function __construct(ContactInfutorSummary $model)
    {
        $this->model = $model;
    }


    public function store(array $data){
        try {
            $store = $this->model->create($data);
            if($store){
                return $store;
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

}