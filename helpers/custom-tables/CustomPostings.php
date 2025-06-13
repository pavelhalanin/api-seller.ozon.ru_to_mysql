<?php

class CustomPostings {
    static function main() {
        global $HOME;

        $text = file_get_contents("$HOME/temp/raw-database/v3_finance_transaction_list.json");
        $data = json_decode($text, true);
        $transactionArray = $data['data'];

        $data = CustomPostings::getPostings_byTransactionArray($transactionArray);

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $JSON = [
            'updated_at' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];
        $FILE_PATH = "$folder/custom_postings.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getPostings_byTransactionArray($transactionArray) {
        $unicPostings = [];
        $postingArray = [];
        for ($i = 0; $i < count($transactionArray); $i++) {
            $array_element = $transactionArray[$i];

            $order_date =  $array_element['posting']['order_date'];
            $posting_number =  $array_element['posting']['posting_number'];
            $posting_explode = explode('-', $posting_number);

            $p1 = count($posting_explode) > 0 ? $posting_explode[0] : "";
            $p2 = count($posting_explode) > 1 ? $posting_explode[1] : "";
            $p3 = count($posting_explode) > 2 ? $posting_explode[2] : "";

            $id = "$order_date-$p1-$p2";

            if (in_array($id, $unicPostings)) {
                continue;
            }

            if (strcmp($id, '--') == 0) {
                continue;
            }

            $unicPostings []= $id;
            $postingArray []= [
                'n' => '',
                'order_date' => $order_date,
                'p1' => $p1,
                'p2' => $p2,
                'p3' => '',
            ];
        }

        for ($i = 0; $i < count($postingArray); $i++) {
            $postingArray[$i]['n'] = $i + 1;
        }

        for ($i = 0; $i < count($transactionArray); $i++) {
            $array_element = $transactionArray[$i];

            $i_posting_number =  $array_element['posting']['posting_number'];
            $i_posting_explode = explode('-', $i_posting_number);

            if (count($i_posting_explode) < 3) {
                continue;
            }

            $i_order_date =  $array_element['posting']['order_date'];
            $i_p1 = $i_posting_explode[0];
            $i_p2 = $i_posting_explode[1];
            $i_p3 = $i_posting_explode[2];

            for ($j = 0; $j < count($postingArray); $j++) {
                $j_element = $postingArray[$j];

                $j_order_date = $j_element['order_date'];
                $j_p1 = $j_element['p1'];
                $j_p2 = $j_element['p2'];

                $id_i = "$i_order_date-$i_p1-$i_p2";
                $id_j = "$j_order_date-$j_p1-$j_p2";
                $isEquals = strcmp($id_i, $id_j) == 0;
                if ($isEquals) {
                    $postingArray[$j]['p3'] = $i_p3;
                    break;
                }
            }
        }

        return $postingArray;
    }
}
