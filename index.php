<?php
require_once "requirements.php";


//uvodne nastavanie
$links=curl_links(GIT);
$at=new attendanceController();
$db= new dbController();

//update linkov
foreach ($links as $link){
    $file_name=substr($link,strrpos($link,"/")+1);
    if(!$db->isCsvInTable($file_name)){
        $string= $at->curl($link);
        $csv=$at->toCSV($string);
        if(!is_null($db->createTable($file_name))){
            update_table($db,$csv,$file_name);
        }
    }
}

header('Location: http://wt141.fei.stuba.sk/zadanie_4/page.php');
















