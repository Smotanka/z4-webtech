<?php

function curl_links($git): array
{

    $curl=curl_init();
    $home="https://github.com";
    $raw="https://raw.githubusercontent.com";
    curl_setopt($curl, CURLOPT_URL,$home.$git);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    $html=curl_exec($curl);
    curl_close($curl);

    $doc = new DOMDocument();

    $doc->loadHTML($html,);



    $result=$doc->getElementsByTagName('a');

    $array=[];

    foreach($result as $node) {
        $url=$node->getAttribute('href');
        $url=str_replace ( "/blob", "", $url );
        if(str_ends_with ( $url , ".csv" ))
            $array[] =$raw.$url;

    }
    return $array;
}

