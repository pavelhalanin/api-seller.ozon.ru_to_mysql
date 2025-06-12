<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/helpers/FetchOzonSellerApi.php";

class SaveJson_v3_product_list {
    static function main() {
        global $HOME;

        $data = SaveJson_v3_product_list::fetchJson__getAllProducts();

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $JSON = [
            'update_at' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];

        $FILE_PATH = "$folder/v3_product_list.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getProductIdArray() {
        $arr = SaveJson_v3_product_list::fetchJson__getAllProducts();
        $productIdArray = array_map(function($element) {
            return $element['product_id'];
        }, $arr);
    }

    static function fetchJson__getAllProducts() {
        return array_merge(
            SaveJson_v3_product_list::fetchJson__getNotArchivedProducts()['result']['items'],
            SaveJson_v3_product_list::fetchJson__getArchivedProducts()['result']['items']
        );
    }

    static function fetchJson__getNotArchivedProducts() {
        return SaveJson_v3_product_list::fetchJson([
            'limit' => 1000,
            'filter' => [
                'visibility' => 'ALL',
            ],
        ]);
    }

    static function fetchJson__getArchivedProducts() {
        return SaveJson_v3_product_list::fetchJson([
            'limit' => 1000,
            'filter' => [
                'visibility' => 'ARCHIVED',
            ],
        ]);
    }

    static function fetchJson($data) {
        return FetchOzonSellerApi::fetchJson_byUriAndData(
            "/v3/product/list",
            $data,
        );
    }
}
