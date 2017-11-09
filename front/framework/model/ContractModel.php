<?php

/*
 * 作者：戎青松
 * 时间：9:58:15
 * 
 */

/**
 * Description of HotelModel
 *
 * @author Kevin
 */
__load("Model", "model");
class ContractModel extends Model {

    public function get_Contract($id) {
        $sql = "
         SELECT
       *
    FROM
        " . $this->ecs->table("contract")." AS c
   
    WHERE
        c.`contract_id` = $id";

        return $this->db->getRow($sql);
    }

}
