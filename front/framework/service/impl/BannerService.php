<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BatchService
 *
 * @author qs
 */
class BannerService {

    //put your code here

    private $banner, $place;

    public function __construct() {
        __load("BannerModel", "model");
        __load("BannerplaceModel", "model");
        $this->banner = new BannerModel();
        $this->place = new BannerplaceModel();
    }

    public function get_place_list() {
        return $this->place->get();
    }

    public function insert_place($data) {
        return $this->place->insert($data);
    }

    public function insert_banner($data) {
        return $this->banner->insert($data);
    }

    public function get_place($id) {
        $place = $this->place->getOne($id);
        if (empty($place)) {
            return false;
        } else {
            return $place;
        }
    }

    public function get_banner($id) {
        $banners = $this->banner->getOne($id);
        return $banners;
    }

    public function get_banners() {
        $banners = $this->banner->get();
        return $banners;
    }

    public function get_banner_list($code) {

        $banners = $this->banner->get_for_code($code);
        return $banners;
    }

    public function update_place($data) {
        return $this->place->update($data);
    }

    public function update_banner($data) {
        return $this->banner->update($data);
    }

    public function banner_remove($id) {
        return $this->banner->remove($id);
    }

    public function place_remove($id) {

        return $this->place->remove($id);
    }

}
