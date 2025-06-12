<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/env.php";

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
        global $env;

        $URI = "/v2/posting/fbs/act/get-postings";
        $FETCH_URL = "https://api-seller.ozon.ru$URI";

        $json_string = json_encode($data);
        $http_data = $json_string;

        $ozonClientId = $env['ozon-client-id'];
        $ozonApiKey = $env['ozon-api-key'];

        $http_headers = [
            "Content-Type: application/json",
            "Client-Id: $ozonClientId",
            "Api-Key: $ozonApiKey",
        ];

        $http_cookie = implode("; ", [
            "Client-Id=$ozonClientId",
            "Api-Key=$ozonApiKey",
        ]);

        if ($env['log']) {
            echo "\nPOST $FETCH_URL\n$http_data\n";
        }

        $ch = curl_init($FETCH_URL);                            // Инициализируем cURL сессии
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);                   // Устанавливаем метод POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $http_data);       // Тело запроса
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);    // Устанавливаем заголовки
        curl_setopt($ch, CURLOPT_COOKIE, $http_cookie);         // Передаем куки
        $response = curl_exec($ch);                             // Выполняем запрос и получаем ответ

        if (curl_errno($ch)) {                                  // Проверяем на наличие ошибок
            $err = curl_error($ch);                             // Получаем сообщение об ошибке
            curl_close($ch);                                    // Закрываем cURL сессию
            throw new Error("Fetch error: $err");
        }

        curl_close($ch);                                        // Закрываем cURL сессию
        $json_string = $response;
        $php_object = json_decode($json_string, true);
        return $php_object;
    }
}
