<?php

/**
 * Created by PhpStorm.
 * User: stepreal
 * Date: 17/9/24
 * Time: 上午11:19
 */
__load("Model", "model");

class R201860Model extends Model
{
    public function get_list()
    {
        /* 查询条件 */
        $filter = array();
        $filter['id'] = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $where = (!empty($filter['id'])) ? " AND id = '$filter[id]' " : '';
        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('question_paper_2018') . " WHERE 1 " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " . $this->ecs->table("question_paper_2018") . " WHERE 1 " . $where . "LIMIT " . $filter['start'] . ",$filter[page_size]";
        $row = $this->db->getAll($sql);
        return array('list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }

    public function save($data)
    {
        return $this->db->autoExecute($this->ecs->table("question_paper_2018"), $data, 'INSERT');
    }

    public function update($data)
    {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->autoExecute($this->ecs->table("question_paper_2018"), $data, 'UPDATE', "id=$id");
    }

    public function remove($id)
    {
        $sql = "DELETE FROM " . $this->ecs->table("question_paper_2018") . " WHERE id=$id";
        return $this->db->query($sql);
    }

    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->ecs->table("question_paper_2018") . " WHERE id=$id";
        return $this->db->getRow($sql);
    }


    public function remove_batch($data)
    {
        foreach ($data as $key => $id) {
            $sql = "DELETE FROM" . $this->ecs->table("question_paper_2018") . " WHERE id=$id";
            $res = $this->db->query($sql);
        }
        return $res;
    }
}
