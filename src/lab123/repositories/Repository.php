<?php
namespace FaseObra\Repositories;

use FaseObra\Repositories\Contracts\IRepository;
use FaseObra\Classlib\Utils\Api;

abstract class Repository implements IRepository
{

    protected $model;

    protected $tree_uri = [];

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->find($id)->update($data);
    }

    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $include = null, $fields = null)
    {
        $model = $this->model;
        
        foreach ($criteria as $c) {
            $model = $model->where($c[0], $c[1], $c[2]);
        }
        
        if ($orderBy !== null) {
            foreach ($orderBy as $order) {
                $model = $model->orderBy($order[0], $order[1]);
            }
        }
        
        if ($limit !== null) {
            $model = $model->take((int) $limit);
        }
        
        if ($offset !== null) {
            $model = $model->skip((int) $offset);
        }
        
        if ($include !== null) {
            $model = $model->with($include);
        }
        
        if ($fields !== null) {
            $model = $model->select($fields);
        }
        
        return $model->get();
    }

    public function findOneBy(array $criteria)
    {
        return $this->findBy($criteria)->first();
    }

    public function setRelationships(array $ralationships = [])
    {
        $relations = [];
        if (! empty($ralationships)) {
            $relations = $ralationships;
        } else 
            if (! empty($this->relationships)) {
                $relations = $this->relationships;
            }
        
        foreach ($relations as $relation) {
            $this->model->with($relation);
        }
        
        return $this;
    }

    public function getURI()
    {
        $ids = func_get_args();
        $uri = '';
        
        foreach ($this->tree_uri as $i => $node_uri) {
            
            $uri .= (empty($uri)) ? '' : '/';
            $uri .= $node_uri;
            $uri .= (array_key_exists($i, $ids)) ? '/' . $ids[$i] : '/';
        }
        
        return Api::url() . $uri;
    }
}