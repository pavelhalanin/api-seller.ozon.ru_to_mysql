<?php

$HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

include_once "$HOME/helpers/FetchOzonSellerApi.php";

class SaveJson_v2_posting_fbs_act_list {
    static function main() {
        global $HOME;

        $data = SaveJson_v2_posting_fbs_act_list::getAll();

        $JSON = [
            'updatedAt' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $FILE_PATH = "$folder/v2_posting_fbs_act_list.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getAll() {
        $startDate = '2024-01-10';
        $endDate = (new DateTime('tomorrow'))->format('Y-m-d');

        $result = SaveJson_v2_posting_fbs_act_list::getData($startDate, $endDate);

        $resultArray = [];
        for ($i = 0; $i < count($result); $i++) {
            $data = SaveJson_v2_posting_fbs_act_list::fetchJson__getPostingFbsActListOnPeriod($result[$i]['start'], $result[$i]['end']);
            $arr = $data['result'];
            for ($j = 0; $j < count($arr); $j++) {
                $resultArray []= $arr[$j];
            }
        }

        return $resultArray;
    }

    static function getData($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('last day of this month'); // Получаем последний день месяца для конечной даты
        $data = [];

        while ($start <= $end) {
            $startOfMonth = $start->format('Y-m-01');
            $endOfMonth = $start->format('Y-m-t');

            $data[] = [
                'start' => $startOfMonth,
                'end' => $endOfMonth
            ];

            // Переход к следующему месяцу
            $start->modify('first day of next month');
        }

        return $data;
    }

    static function fetchJson__getPostingFbsActListOnPeriod($dateFrom, $dateTo) {        
        return SaveJson_v2_posting_fbs_act_list::fetchJson([
            "filter" => [
                "date_from" => $dateFrom,
                "date_to" => $dateTo,
            ],
            "limit" => 50,
        ]);
    }

    static function fetchJson($data) {
        return FetchOzonSellerApi::fetchJson_byUriAndData(
            "/v2/posting/fbs/act/list",
            $data,
        );
    }
}
