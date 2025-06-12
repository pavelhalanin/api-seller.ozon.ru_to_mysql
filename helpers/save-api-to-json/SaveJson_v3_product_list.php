<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/env.php";

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
        global $env;

        $URI = "/v3/product/list";
        $FETCH_URL = "https://api-seller.ozon.ru$URI";

        $string_json = json_encode($data);
        $http_data = $string_json;

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
