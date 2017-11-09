<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/24
 * Time: 上午11:18
 */
class R201860Service
{
    public function __construct()
    {
        __load("R201860Model", "model");
        $this->r201860Model = new R201860Model();
    }

    public function getById($id)
    {
        return $this->r201860Model->get($id);
    }

    public function get_list()
    {
        return $this->r201860Model->get_list();
    }

    public function get_r201860($id)
    {
        return $this->r201860Model->get($id);
    }

    public function save($data)
    {
        return $this->r201860Model->save($data);
    }

    public function update($data)
    {
        return $this->r201860Model->update($data);
    }

    public function remove($id)
    {
        return $this->r201860Model->remove($id);
    }

    public function remove_b($data)
    {
        return $this->r201860Model->remove_batch($data);
    }
}