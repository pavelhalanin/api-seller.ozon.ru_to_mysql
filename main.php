<?php

try {
    $HOME = strlen($_SERVER['DOCUMENT_ROOT']) != 0 ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PHP_CRON_HOME'];

    include_once "$HOME/helpers/save-api-to-json/SaveJson_v3_product_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v3_product_info_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v4_product_info_attributes.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v3_finance_transaction_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v1_warehouse_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v1_returns_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v1_rating_summary.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v1_delivery_method_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v1_description_category_tree.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v2_posting_fbs_act_list.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v2_posting_fbs_act_get_postings.php";
    include_once "$HOME/helpers/save-api-to-json/SaveJson_v1_description_category_attribute.php";
    include_once "$HOME/helpers/custom-tables/CustomPostings.php";
    include_once "$HOME/helpers/custom-tables/CountProducts.php";
    include_once "$HOME/helpers/reports/ReportInventarizaciya.php";
    include_once "$HOME/helpers/reports/ReportInventarizaciyaAsCsv.php";

    SaveJson_v3_product_list::main();
    SaveJson_v3_product_info_list::main();
    SaveJson_v4_product_info_attributes::main();
    SaveJson_v3_finance_transaction_list::main();
    SaveJson_v1_warehouse_list::main();
    SaveJson_v1_returns_list::main();
    SaveJson_v1_rating_summary::main();
    SaveJson_v1_delivery_method_list::main();
    SaveJson_v1_description_category_tree::main();
    SaveJson_v2_posting_fbs_act_list::main();
    SaveJson_v2_posting_fbs_act_get_postings::main();
    SaveJson_v1_description_category_attribute::main();
    CustomPostings::main();
    CustomCountProducts::main();
    ReportInventarizaciya::main();
    ReportInventarizaciyaAsCsv::main();
}
catch(Throwable $exception) {
    echo "<pre>";
    print_r($exception);
    echo "</pre>";
}
