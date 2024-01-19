<?php

namespace LaravelCake\Lead\Repositories;

/**
 * Interface BaseRepositoryInterface
 *
 * @package App\Repositories
 */
interface BaseRepositoryInterface
{
    /**
     * Find a model by its ID.
     *
     * @param  int $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * Create a new model instance.
     *
     * @param  array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update a model by its ID.
     *
     * @param  int   $id
     * @param  array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * Delete a model by its ID.
     *
     * @param  int $id
     * @return mixed
     */
    public function delete(int $id);


    /**
     * insert
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data);
}
