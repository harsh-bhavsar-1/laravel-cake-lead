<?php

namespace LaravelCake\Lead\Repositories;

use LaravelCake\Lead\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseRepository
 *
 * @package App\Repositories
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Find a model by its ID.
     *
     * @param  int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new model instance.
     *
     * @param  array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a model by its ID.
     *
     * @param  int   $id
     * @param  array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $model = $this->find($id);

        if ($model) {
            $model->update($data);
        }

        return $model;
    }

    /**
     * Delete a model by its ID.
     *
     * @param  int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $model = $this->find($id);

        if ($model) {
            $model->delete();
        }

        return $model;
    }

    /**
     * insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert(array $data)
    {
        return $this->model::insert($data);
    }

    /**
     * deleteByWebhook
     *
     * @param  int $id
     * @return void
     */
    public function deleteByWebhook(int $id)
    {
        return $this->model->where('webhook_id', $id)->delete();
    }

    /**
     * findByWebhook
     *
     * @param  int $id
     */
    public function findByWebhook(int $id)
    {
        return $this->model->where('webhook_id', $id)->get();
    }

    /**
     * bulkdDelete
     *
     * @param  array $data
     * @return void
     */
    public function bulkdDelete(array $data)
    {
        return $this->model->whereIn('id', $data)->delete();
    }
}
