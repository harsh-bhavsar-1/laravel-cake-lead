<?php
namespace laravelcake\lead;
use LaravelCake\Lead\Services\ContactService;

class Contact
{
    public $contactService;
    
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function store($arr = []){
        return $this->contactService->store($arr);
    }
}
