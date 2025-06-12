<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/helpers/FetchOzonSellerApi.php";

class SaveJson_v4_product_info_attributes {
    static function main() {
        global $HOME;

        $text = file_get_contents("$HOME/temp/raw-database/v3_product_list.json");
        $data = json_decode($text, true);
        $array = $data['data'];

        $product_id_array = array_map(function ($element) {
            return $element['product_id'];
        }, $array);

        $data = SaveJson_v4_product_info_attributes::fetchJson([
            'filter' => [
                'product_id' => $product_id_array
            ],
            'limit' => 1000,
        ])['result'];

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $JSON = [
            'updated_at' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];
        $FILE_PATH = "$folder/v4_product_info_attributes.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function fetchJson($data) {
        return FetchOzonSellerApi::fetchJson_byUriAndData(
            "/v4/product/info/attributes",
            $data,
        );
    }
}
