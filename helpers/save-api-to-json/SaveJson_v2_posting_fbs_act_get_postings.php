<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/helpers/FetchOzonSellerApi.php";

class SaveJson_v2_posting_fbs_act_get_postings {
    static function main() {
        global $HOME;

        $jsonString = file_get_contents("$HOME/temp/raw-database/v2_posting_fbs_act_list.json");

        $data = json_decode($jsonString, true);

        $array = $data['data'];

        $result_array = [];
        for ($i = 0; $i < count($array); $i++) {
            $current = $array[$i];
            $current_id = $current['id'];
            $current_more_data = SaveJson_v2_posting_fbs_act_get_postings::getById($current_id);
            array_push($result_array, [
                'id' => $current_id,
                'data' => $current,
                'more' => $current_more_data,
            ]);
        }

        $data = $result_array;

        $JSON = [
            'updatedAt' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $FILE_PATH = "$folder/v2_posting_fbs_act_get_postings.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getById($id) {
        return SaveJson_v2_posting_fbs_act_get_postings::fetchJson([
            'id' => $id,
        ]);
    }

    static function fetchJson($data) {
        return FetchOzonSellerApi::fetchJson_byUriAndData(
            "/v2/posting/fbs/act/get-postings",
            $data,
        );
    }
}
