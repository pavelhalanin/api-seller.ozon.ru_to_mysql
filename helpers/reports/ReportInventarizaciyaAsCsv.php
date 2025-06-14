<?php

class ReportInventarizaciyaAsCsv {
    static function main() {
        global $HOME;

        $text = file_get_contents("$HOME/temp/reports/report_inventarizaciya.json");
        $data = json_decode($text, true);
        $array = $data['data'];

        $csv = ReportInventarizaciyaAsCsv::getCsv_byArray($array);

        $folder = "$HOME/temp/reports";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true); 
        }

        $FILE_PATH = "$folder/report_inventarizaciya.csv";
        $FILE_TEXT = $csv;
        file_put_contents($FILE_PATH, $FILE_TEXT);
    }

    static function getCsv_byArray($array) {
        $csv = "";

        $csv .= implode("\t", [
            'id',
            'Бренд',
            'Картинка',
            'Модель',
            'FB',
            'Количество',
            'Наименование',
            'Штрихкод',
            'Длина',
            'Глубина',
            'Высота',
            'Вес',
        ]) . "\n";

        for ($i = 0; $i < count($array); $i++) {
            $arrayElement = $array[$i];
            $csv .= implode("\t", [
                $arrayElement['product_id'],
                $arrayElement['attribute_85_brand'],
                '=IMAGE("' . $arrayElement['primary_image'] . '")',
                $arrayElement['offer_id'],
                $arrayElement['fb_type'],
                $arrayElement['count'],
                $arrayElement['name'],
                $arrayElement['barcode'],
                $arrayElement['width'],
                $arrayElement['depth'],
                $arrayElement['height'],
                $arrayElement['weight'],
            ]) . "\n";
        }

        return $csv;
    }
}
