<?php

$main_url = 'https://www.oxfordlearnersdictionaries.com/wordlist/english/oxford3000/Oxford3000_A-B/';
$html = file_get_contents($main_url); //get the html returned from the following url
//echo $html;
$pokemon_doc = new DOMDocument();

libxml_use_internal_errors(TRUE); //disable libxml errors

if (!empty($html)) { //if any html is actually returned
    $pokemon_doc->loadHTML($html);
    libxml_clear_errors(); //remove errors for yucky html

    $pokemon_xpath = new DOMXPath($pokemon_doc);

    //get all the h2's with an id
//	$pokemon_row = $pokemon_xpath->query('//h2[@id]');
//    $pokemon_row = $pokemon_xpath->query('//div[@id="entrylist1"]');
//
//    if ($pokemon_row->length > 0) {
//        foreach ($pokemon_row as $row) {
//            $data_infos = $pokemon_xpath->query('//ul[@class="result-list1 wordlist-oxford3000 list-plain"]/li/a', $row);
//            foreach ($data_infos as $data_info) {
//                $data[] = $data_info->nodeValue;
//            }
//        }
//        dbInsert($data);
//    }

//    $pagination = $pokemon_xpath->query('//li[@class="activepage"]/span');
//    if ($pagination->length > 0) {
//        $page_no = $pagination[0]->nodeValue;
//        LoopTest($main_url, $page_no);
//    }
//
    $next_serarch = $pokemon_xpath->query('//ul[@class="hide_phone"]/li/a');
    $keyword[] = 'A-B';
    if ($next_serarch->length > 0) {
        foreach ($next_serarch as $row) {
            $keyword[] = $row->nodeValue;
        }
    }
//    echo '<pre>';
//    print_r($keyword);
//    exit();
    foreach ($keyword as $step) {
        $newURL = "https://www.oxfordlearnersdictionaries.com/wordlist/english/oxford3000/Oxford3000_" . $step;
        LoopTest($newURL, 0);
    }
}

function LoopTest($main_url, $page_no) {
    $previous_page_no = $page_no;
    $url = $main_url . "/?page=" . ($page_no + 1);


    $html = file_get_contents($url); //get the html returned from the following url
//echo $html;
    $pokemon_doc = new DOMDocument();

    libxml_use_internal_errors(TRUE); //disable libxml errors

    if (!empty($html)) { //if any html is actually returned
        $pokemon_doc->loadHTML($html);
        libxml_clear_errors(); //remove errors for yucky html

        $pokemon_xpath = new DOMXPath($pokemon_doc);

        //get all the h2's with an id
//	$pokemon_row = $pokemon_xpath->query('//h2[@id]');
        $pokemon_row = $pokemon_xpath->query('//div[@id="entrylist1"]');

        if ($pokemon_row->length > 0) {
            foreach ($pokemon_row as $row) {
                $data_infos = $pokemon_xpath->query('//ul[@class="result-list1 wordlist-oxford3000 list-plain"]/li/a', $row);
                foreach ($data_infos as $data_info) {
                    $data[] = $data_info->nodeValue;
                }
            }
            if (!empty($data)) {
                dbInsert($data);
            }
        }

        $pagination = $pokemon_xpath->query('//li[@class="activepage"]/span');

        if ($pagination->length > 0) {
            $page_no = $pagination[0]->nodeValue;
            if ($previous_page_no < $page_no) {
                LoopTest($main_url, $page_no);
            }
        }
    }
}

function dbInsert($data) {

    $b = null;
    foreach ($data as $key => $row) {
        if ($key == 0) {
            $a = "('$row')";
        } else {
            $a = ",('$row')";
        }
        $b .= $a;
    }

    $conn = mysqli_connect("localhost", "root", "", "english_db");

// Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $sql = "INSERT INTO tbl_en_bn (en)
VALUES $b;";

    echo $sql;
    echo '<br>';

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}


?>