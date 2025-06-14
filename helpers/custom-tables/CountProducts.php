<?php

class CustomCountProducts {
    static function main() {
        global $HOME;

        $text = file_get_contents("$HOME/temp/raw-database/v3_product_info_list.json");
        $data = json_decode($text, true);
        $productInfoList = $data['data'];

        $data = CustomCountProducts::getArrayCountProducts_byProductInfoList($productInfoList);

        $folder = "$HOME/temp/raw-database";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $JSON = [
            'updated_at' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];
        $FILE_PATH = "$folder/custom_count_products.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getArrayCountProducts_byProductInfoList($productInfoList) {
        $resultArray = [];
        for ($i = 0; $i < count($productInfoList); $i++) {
            $array_element = $productInfoList[$i];

            $productId = $array_element['id'];
            $stocks = $array_element['stocks']['stocks'];

            if (count($stocks) == 0) {
                $resultArray []= [
                    'product_id' => $productId,
                    'count' => 0,
                    'reserved' => 0,
                    'sku' => '',
                    'source' => '',
                ];
                continue;
            }

            for ($j = 0; $j < count($stocks); $j++) {
                $resultArray []= [
                    'product_id' => $productId,
                    'count' => $stocks[$j]['present'],
                    'reserved' => $stocks[$j]['reserved'],
                    'sku' => $stocks[$j]['sku'],
                    'source' => $stocks[$j]['source'],
                ];
            }
        }

        return $resultArray;
    }
}
