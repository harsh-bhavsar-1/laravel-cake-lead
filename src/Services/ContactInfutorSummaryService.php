<?php

namespace LaravelCake\Lead\Services;

use LaravelCake\Lead\Services\BaseService;
use Exception;
use LaravelCake\Lead\Repositories\ContactInfutorSummaryRepository;

/**
 * Class AuthService
 *
 * @package App\Services
 */
class ContactInfutorSummaryService extends BaseService
{
    protected $contactInfutorSummaryRepository;

    /**
     * __construct
     *
     * @param  ContactRepository $contactRepository
     * @return void
     */
    public function __construct(ContactInfutorSummaryRepository $contactInfutorSummaryRepository)
    {
        $this->contactInfutorSummaryRepository = $contactInfutorSummaryRepository;
    }


    public function store(array $data){
        try {
            $response = $this->contactInfutorSummaryRepository->store($data);
            if($response){
                return $response;
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
}
