<?php

class ReportInventarizaciya {
    static function main() {
        global $HOME;

        $text = file_get_contents("$HOME/temp/raw-database/custom_count_products.json");
        $data = json_decode($text, true);
        $array_count_products = $data['data'];

        $text = file_get_contents("$HOME/temp/raw-database/v4_product_info_attributes.json");
        $data = json_decode($text, true);
        $products_array = $data['data'];

        $data = ReportInventarizaciya::getResult_byArrayCountProductAndProductArray(
            $array_count_products,
            $products_array,
        );

        $folder = "$HOME/temp/reports";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $JSON = [
            'updated_at' => date('Y-m-d_H-i-s'),
            'data' => $data,
        ];
        $FILE_PATH = "$folder/report_inventarizaciya.json";
        $FILE_TEXT = json_encode($JSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getResult_byArrayCountProductAndProductArray($array_count_products, $products_array) {
        $resultArray = [];

        for ($i = 0; $i < count($array_count_products); $i++) {
            $data_count_product = $array_count_products[$i];

            $productId = $data_count_product['product_id'];
            $fb_type = $data_count_product['source'];
            $productCount = $data_count_product['count'];
           
            $resultArray []= [
                'product_id' => $productId,
                'count' => $productCount,
                'fb_type' => $fb_type,
            ];
        }

        for ($i = 0; $i < count($resultArray); $i++) {
            $data_count_product = $resultArray[$i];

            $i_productId = $resultArray[$i]['product_id'];
            $resultArray[$i]['primary_image'] = '';
            $resultArray[$i]['barcode'] = '';
            $resultArray[$i]['offer_id'] = '';
            $resultArray[$i]['name'] = '';
            $resultArray[$i]['width'] = '';
            $resultArray[$i]['depth'] = '';
            $resultArray[$i]['height'] = '';
            $resultArray[$i]['weight'] = '';
            $resultArray[$i]['barcode'] = '';
            $resultArray[$i]['attribute_85_brand'] = '';

            for ($j = 0; $j < count($products_array); $j++) {
                $j_productId = $products_array[$j]['id'];

                
                if (strcmp($i_productId, $j_productId) == 0) {
                    $resultArray[$i]['primary_image'] = $products_array[$j]['primary_image'];
                    $resultArray[$i]['barcode'] = $products_array[$j]['barcode'];
                    $resultArray[$i]['offer_id'] = $products_array[$j]['offer_id'];
                    $resultArray[$i]['name'] = $products_array[$j]['name'];
                    $resultArray[$i]['width'] = $products_array[$j]['width'];
                    $resultArray[$i]['depth'] = $products_array[$j]['depth'];
                    $resultArray[$i]['height'] = $products_array[$j]['height'];
                    $resultArray[$i]['weight'] = $products_array[$j]['weight'];
                    $resultArray[$i]['barcode'] = $products_array[$j]['barcode'];
                    $attributes = $products_array[$j]['attributes'];
                    for ($k = 0; $k < count($attributes); $k++) {
                        $attributeId = $attributes[$k]['id'];
                        if (strcmp($attributeId, 85) == 0) {
                            $resultArray[$i]['attribute_85_brand'] = $attributes[$k]['values'][0]['value'];
                            break;
                        }
                    }
                }
            }

        }

        return $resultArray;
    }
}
