<?php

$newURL = "http://www.english-bangla.com/posts/animal_voices";
 LoopTest($newURL, 1);
//for ($i = 0; $i < 2; $i++) {
//    LoopTest($newURL, $i);
//}

function LoopTest($main_url, $page_no) {
//    $previous_page_no = $page_no;
//    $url = $main_url . "/" . ($page_no + 1);

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
        $pokemon_row = $pokemon_xpath->query('//div[@class="posts"]');
   
        if ($pokemon_row->length > 0) {
            foreach ($pokemon_row as $row) {
                $data_infos = $pokemon_xpath->query('//div[@class="posts"]//p', $row);
//                $data_infos = $pokemon_xpath->query('//p', $row);
               
                foreach ($data_infos as $data_info) {
                    $data[] = $data_info->nodeValue;
                }
            }
        }
    
        if (!empty($data)) {
            dbInsert($data);
        }
    }
}

function dbInsert($data) {
//    echo '<pre>';
//    print_r($data);
//    exit();

    $b = null;
    foreach ($data as $key => $row) {
      $row =  str_replace("'","\\","$row");
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

    $sql = "INSERT INTO tbl_animal_voices (animal_voices)
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