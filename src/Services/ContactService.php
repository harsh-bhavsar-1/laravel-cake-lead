<?php

namespace LaravelCake\Lead\Services;

use LaravelCake\Lead\Repositories\ContactRepository;
use LaravelCake\Lead\Services\BaseService;
use Exception;

/**
 * Class AuthService
 *
 * @package App\Services
 */
class ContactService extends BaseService
{
    protected $contactRepository;

    /**
     * __construct
     *
     * @param  ContactRepository $contactRepository
     * @return void
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function list(){
        try {
            $response = $this->contactRepository->list();
            return $response;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function store(array $data){
        try {
            $response = $this->contactRepository->store($data);
            if($response){
                return $response;
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function update($id, $data){
        try {
            $response = $this->contactRepository->update($id, $data);
            if($response){
                return $response;
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function createRedirectUrl($request, $inputs){
        try{
            $response = $this->contactRepository->createRedirectUrl($request, $inputs);
            if($response){
                return $response;
            }
            return false;
        }catch (Exception $ex) {
            return false;
        }
    }

    public function universalLeadData($inputs){
        try{
            $response = $this->contactRepository->universalLeadData($inputs);
            if($response){
                return $response;
            }
            return false;
        }catch (Exception $ex) {
            return false;
        }
    }

}
