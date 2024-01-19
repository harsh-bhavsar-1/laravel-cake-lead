<?php

namespace LaravelCake\Lead\Services;

/**
 * Class BaseService
 *
 * @package LaravelCake\Lead\Service
 */
class BaseService
{
    /**
     * Create a failed response.
     *
     * @param  int $status
     * @param  string $message
     * @return array
     */

    protected function failedResponse(int $status, string $message): array
    {
        return [
            'status_code' => $status,
            'success' => false,
            'status' => 'failed',
            'msg' => $message,
        ];
    }

    /**
     * Create an error response.
     *
     * @param  int $status
     * @param  string $message
     * @param  array $data
     * @return array
     */
    protected function successResponse(int $status, string $message, array $data): array
    {
        return [
            'status_code' => $status,
            'success' => true,
            'status' => 'success',
            'msg' => $message,
            'data' => $data,
        ];
    }

    /**
     * filteredArray
     *
     * @param  array $array
     * @return array
     */
    protected function filteredArray(array $array): array
    {

        $filteredArray = array_values(array_filter($array, function ($value) {
            return $value !== null || "";
        }));

        return $filteredArray;
    }
}
