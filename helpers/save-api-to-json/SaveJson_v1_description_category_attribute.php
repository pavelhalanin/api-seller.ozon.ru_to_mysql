<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/helpers/FetchOzonSellerApi.php";

class SaveJson_v1_description_category_attribute {
    static function main() {
        global $HOME;

        $text = file_get_contents("$HOME/temp/raw-database/v4_product_info_attributes.json");
        $data = json_decode($text, true);
        $array = $data['data'];

        $unicValueArray = [];
        $arrayData = [];
        for ($i = 0; $i < count($array); $i++) {
            $description_category_id = $array[$i]['description_category_id'];
            $type_id = $array[$i]['type_id'];
            $value = "$description_category_id-$type_id";
            if (in_array($value, $unicValueArray)) {
                continue;
            }

            $unicValueArray []= $value;
            $arrayData []= [
                'description_category_id' => $description_category_id,
                'type_id' => $type_id,
            ];
        }

        $result_array = [];
        for ($i = 0; $i < count($arrayData); $i++) {
            $description_category_id = $arrayData[$i]['description_category_id'];
            $language = 'RU';
            $type_id = $arrayData[$i]['type_id'];

            $arr = SaveJson_v1_description_category_attribute::fetchJson([
                "description_category_id" => $description_category_id,
                "language" => $language,
                "type_id" => $type_id,
            ])['result'];

            for ($j = 0; $j < count($arr); $j++) {
                $arr[$j]['description_category_id'] = $description_category_id;
                $arr[$j]['language'] = $language;
                $arr[$j]['type_id'] = $type_id;

                $result_array []= $arr[$j];
            }
        }

        $JSON = [
            'updatedAt' => date('Y-m-d_H-i-s'),
            'data' => $result_array,
        ];

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $FILE_PATH = "$folder/v1_description_category_attribute.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function fetchJson($data) {
        return FetchOzonSellerApi::fetchJson_byUriAndData(
            "/v1/description-category/attribute",
            $data,
        );
    }
}
