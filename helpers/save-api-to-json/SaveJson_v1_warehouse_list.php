<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/helpers/FetchOzonSellerApi.php";

class SaveJson_v1_warehouse_list {
    static function main() {
        global $HOME;

        $data = SaveJson_v1_warehouse_list::fetchJson();

        $FILE_PATH = "$HOME/temp/raw-database/v1_warehouse_list.json";

        $JSON = [
            'updatedAt' => date('Y-m-d_H-i-s'),
            'data' => $data['result'],
        ];

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $FILE_PATH = "$folder/v1_warehouse_list.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function fetchJson() {
        return FetchOzonSellerApi::fetchJson_byUriAndData(
            "/v1/warehouse/list",
            null,
        );
    }
}
