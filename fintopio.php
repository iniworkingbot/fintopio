<?php
error_reporting(0);
$list_query = array_filter(@explode("\n", str_replace(array("\r", " "), "", @file_get_contents(readline("[?] List Query       ")))));
echo "[*] Total Query : ".count($list_query)."\n";
for ($i = 0; $i < count($list_query); $i++) {
    $c = $i + 1;
    echo "\n[$c]\n";
    $auth = get_auth($list_query[$i]);
    echo "[*] Get Auth : ";
    if($auth){
        echo "success\n";
        $task = get_task($auth);
        echo "[*] Get Task : ";
        if($task){
            echo "success\n\n";
            for ($a = 0; $a < count($task); $a++) {
                $ex = explode("|", $task[$a]);
                echo "[-] ".$ex[1]."\n";
                echo "\t[>] Start Task : ".start($ex[0], $auth)."\n";
                echo "\t[>] Verify Task : ".verify($ex[0], $auth)."\n";
            }
            sleep(15);
            $task = get_task($auth);
            for ($b = 0; $b < count($task); $b++) {
                $ex = explode("|", $task[$b]);
                echo "[-] ".$ex[1]."\n";
                echo "\t[>] Solve Task : ".solve_task($ex[0], $auth)."\n";
            }
            sleep(15);
            $task = get_task($auth);
            for ($b = 0; $b < count($task); $b++) {
                $ex = explode("|", $task[$b]);
                echo "[-] ".$ex[1]."\n";
                echo "\t[>] Solve Task : ".solve_task($ex[0], $auth)."\n";
            }
        }
        else{
            echo "failed\n\n";
        }
    }
    else{
        echo "failed\n\n";
    }
}
    


function get_auth($query){
    $curl = curl("auth/telegram?$query")['token'];
    return $curl;
}

function get_task($auth){
    $curl = curl("hold/tasks", $auth)['tasks'];
    for ($i = 0; $i < count($curl); $i++) {
        $list[] = $curl[$i]['id']."|".$curl[$i]['slug'];
    }
    return $list;
}

function start($id, $auth){
    $curl = curl("hold/tasks/$id/start", $auth, "{}")['status'];
    return $curl;
}

function verify($id, $auth){
    $curl = curl("hold/tasks/$id/verify", $auth, "{}")['status'];
    return $curl;
}

function solve_task($id, $auth){
    $curl = curl("hold/tasks/$id/claim", $auth, "{}")['status'];
    return $curl;
}

function curl($path, $auth = false, $body = false){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fintopio-tg.fintopio.com/api/'.$path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($body){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $headers = array();
    $headers[] = 'Accept: application/json, text/plain, */*';
    $headers[] = 'Accept-Language: en-US,en;q=0.9';
    if($auth){
        $headers[] = 'Authorization: Bearer '.$auth;
    }
    $headers[] = 'Origin: https://fintopio-tg.fintopio.com';
    $headers[] = 'Referer: https://fintopio-tg.fintopio.com/';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 Edg/128.0.0.0';
    $headers[] = 'Webapp: true';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $decode = json_decode($result, true);
    return $decode;
}



