<?php

// By Dr_Black
// 从IP地址获取cityCode 仅适用于中国大陆以及港澳地区
function GetAdressFromIp($user_ip){
    
    // 构建请求URL
    $url = "https://whois.pconline.com.cn/ipJson.jsp?ip=" . $user_ip . "&json=true";

    // 发送GET请求并获取响应
    $response = file_get_contents($url);

    // 检查是否成功获取响应
    if ($response !== false) {
        // 解析JSON响应
        $fixedResponse = preg_replace('/[\r\n\t]+/', '', $response);
        $fixedResponse = mb_convert_encoding($fixedResponse, 'UTF-8');
        $json_data = json_decode($fixedResponse, true);

        // 检查JSON数据是否包含cityCode
        if (isset($json_data['cityCode'])) {
            // 提取并输出cityCode值
            
            $result = [
                'cityCode' => $json_data['cityCode'],
                'proCode' => $json_data['proCode']
            ];
            
            return $result;
        } else {
            // 输出错误消息，表示无法获取cityCode值
            return $json_data;
            exit();
        }
    } else {
        // 输出错误消息，表示无法获取响应
        return "Error: Unable to fetch response.";
        exit();
    }
}

function GetAdressFromIp_Backup($user_ip){
    $GDMap_apikey = ""; // 填入你的高德开放平台apikey 自行申请
    $url = "https://restapi.amap.com/v3/ip?ip=$user_ip&output=json&key=$GDMap_apikey";
    
    $response = file_get_contents($url);
    
    // 检查是否成功获取响应
    if ($response !== false) {
        // 解析 JSON 响应
        $fixedResponse = preg_replace('/[\r\n\t]+/', '', $response);
        $fixedResponse = mb_convert_encoding($fixedResponse, 'UTF-8');
        $AdressData = json_decode($fixedResponse, true);

        // 检查是否成功解析 JSON 数据
        if (isset($AdressData['adcode'])) {
            // 提取所需字段
            $cityCode = $AdressData['adcode'];
            return $cityCode;

        } else {
            return "Error: JSON解析失败。";
            exit();
        }
    } else {
        // 输出错误消息，表示无法获取响应
        return "Error: Unable to fetch response.";
        exit();
    }
    
}

function getWeatherInfo($F_cityCode) {
    // 构建请求 URL 
    $GDMap_apikey = ""; // 填入你的高德开放平台apikey 自行申请
    $url = "https://restapi.amap.com/v3/weather/weatherInfo?city=$F_cityCode&key=$GDMap_apikey";
    // echo $url;

    // 发送 GET 请求并获取响应

    $response = file_get_contents($url);

    // 检查是否成功获取响应
    if ($response !== false) {
        // 解析 JSON 响应
        $fixedResponse = preg_replace('/[\r\n\t]+/', '', $response);
        $fixedResponse = mb_convert_encoding($fixedResponse, 'UTF-8');
        $weatherData = json_decode($fixedResponse, true);

        // 检查是否成功解析 JSON 数据
        if ($weatherData !== null && isset($weatherData['lives'][0])) {
            // 提取所需字段
            $weather = $weatherData['lives'][0];
            $result = [
                'province' => $weather['province'],
                'city' => $weather['city'],
                'weather' => $weather['weather'],
                'temperature' => $weather['temperature'],
                'windpower' => $weather['windpower'],
                'winddirection' => $weather['winddirection'],
                'humidity' => $weather['humidity'],
                'reporttime' => $weather['reporttime']
            ];
            return $result;
        } else {
            return "Error: JSON解析失败。";
            exit();
        }
    } else {
        // 输出错误消息，表示无法获取响应
        return "Error: Unable to fetch response.";
        exit();
    }
}


function ChatGPT_GEN($text, $lang){
    $url = '你需要接入的ChatGPT接口URL';// 聊天接口
    $language = '请注意xx和适当xx哦~';
    
    $api_key = '';// ChatGPT API KEY
    
    // Request headers
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    );
    
    // Request data
    $data = array(
        // 'model' => 'text-davinci-003',
        'model' => 'gpt-3.5-turbo-0125', // 聊天模型
        // 'model' => 'text-curie-001',
        'temperature' => 1.5,
        // 'prompt' => '如何用php使用chatgpt的聊天接口', // 聊天不用
        'max_tokens' => 3000,
        'messages' => [
            ["role" => "user", "content" => $text . " 从中提取天气用25个字描述，参考以下模板
    深圳市今天天气阴，气温24度，湿度百分之88，东风微风。" . $language],
        ]
    
    );
    
    // Send request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Print response
    // 检查是否成功获取响应
    if ($response !== false) {
        // 解析 JSON 响应
        $fixedResponse = preg_replace('/[\r\n\t]+/', '', $response);
        $fixedResponse = mb_convert_encoding($fixedResponse, 'UTF-8');
        $GPTData = json_decode($fixedResponse, true);

        // 检查是否成功解析 JSON 数据
        if ($GPTData !== null && isset($GPTData['choices'][0])) {
            // 提取所需字段
            $GPTAnswer = $GPTData['choices'][0]['message']['content'];
            return $GPTAnswer;
        } else {
            //$text2 = $text . " 从中提取天气用25个字描述，参考以下模板
    //深圳市今天天气阴，气温24度，湿度百分之88，东风微风。" . $language;
            //echo $text2;
            //echo 'GPT错误';
            print_r($response);
            exit();
        }
    } else {
        // 输出错误消息，表示无法获取响应
        echo "Error: Unable to fetch response.";
        exit();
    }
}


function Mygo_Vits($text, $speaker, $city, $lang){
    $speed = '1.15'; // 语速相关
    if($lang !== 'zh'){
        $speed = '0.95';
    }
    
    $postData = array(
        'text' => $text,
        'sdp_ratio' => '0.5',
        'noise_scale' => '0.6',
        'noise_scale_w' => '0.667',
        'length_scale' => $speed,
        'weather_city' => $city,
        'speaker' => $speaker,
        'base64' => 'false'
    );

    $url = '你的VITS服务器的URL';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if($response === false) {
        return "Error: 无法获取响应。";
        exit();
    } else {
        $fixedResponse = preg_replace('/[\r\n\t]+/', '', $response);
        $fixedResponse = mb_convert_encoding($fixedResponse, 'UTF-8');
        $VITSData = json_decode($fixedResponse, true);

        if ($VITSData !== null && isset($VITSData['audio_url'])) {
            $result = [
                'audio_url' => $VITSData['audio_url'],
                'zh_CN-number' => $VITSData['zh_CN-number'],
                'ja_JP-number' => $VITSData['ja_JP-number'],
                'is_weather' => $VITSData['is_weather'],
                'text' => $text
            ];
            
            $result = json_encode($result);
            return $result;
        } else {
            return "Error: JSON解析失败。";
            exit();
        }
    }
}


function isJapanese($username) {
    // 使用正则表达式匹配日文字符
    // 这里的正则表达式只匹配平假名、片假名、汉字以及常用的日文标点符号
    $pattern = '/^[\p{Hiragana}\p{Katakana}\p{Han}\p{Punctuation}]+$/u';
    return preg_match($pattern, $username);
}


function googleTranslation($text){
    $g_key = '' // 谷歌翻译的API KEY，同样自行申请。;
    
    $postData = array(
        'q' => $text,
        'source' => 'zh',
        'target' => 'ja',
        'format' => 'text',
        'key' => $g_key
    );
    
    $url = 'https://translation.googleapis.com/language/translate/v2';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    
    if($response === false) {
        return "Error: 无法获取响应。";
        exit();
    } else {
        $fixedResponse = preg_replace('/[\r\n\t]+/', '', $response);
        $fixedResponse = mb_convert_encoding($fixedResponse, 'UTF-8');
        $TransData = json_decode($fixedResponse, true);
        
        if ($TransData !== null && isset($TransData['data']['translations'][0]['translatedText'])) {
            //$result = [
            //    'translatedText' => ($TransData['data']['translations'][0]['translatedText'])
            //];
            
            //$result = json_encode($result);
            return $TransData['data']['translations'][0]['translatedText'];
        } else {
            //print_r($TransData);
            return "Error: JSON解析失败。";
            exit();
        }
    }
    
}

###开始###

$cityCode = '';
$area = '';


if ((!isset($_GET['lang']) or $_GET['speaker'] == '')){
    $lang = 'zh';
}else if($_GET['lang'] == 'jp'){
    $lang = 'jp';
}else{
    $lang = 'zh';
}

#判断是否有指定角色
if ((!isset($_GET['speaker']) or $_GET['speaker'] == '')){
    echo "Error: 没有指定对应的角色。";
    exit();
}else{
    $speaker = $_GET['speaker'];
}

// 检查cityCode
if (isset($_GET['citycode']) and ($_GET['citycode'] !== 0) ) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $cityCode = $_GET['citycode'];
    
    if((!isset($_GET['username']) or $_GET['username'] == '')){
        $username = '捏';// 默认的称呼
        $username_input = false;
    }else{
        $username = $_GET['username'];
        $username_input = true;
    }
    
} else if (isset($_GET['procode']) and ($_GET['procode'] !== '999999')) {

    // 港澳地区的判断
    $ip = $_SERVER['REMOTE_ADDR'];
    $cityCode = $_GET['procode'];
    $area = 'sp';
    
    if((!isset($_GET['username']) or $_GET['username'] == '')){
        $username = '捏';// 默认的称呼
        $username_input = false;
    }else{
        $username = $_GET['username'];
        $username_input = true;
    }
    
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if($ip == ''){
        echo "Error: 无法定位您所在的地址。";
        exit();
    }
    
    $code = (GetAdressFromIp($ip));
    $cityCode = $code['cityCode'];
    $proCode = $code['proCode'];
    if($proCode == '999999'){
        echo "Error: 暂时不支持海外地址的天气返回。";
        echo $ip;
        exit();
    }
    if($cityCode == '0' and $proCode !== '999999'){

        // 港澳地区的判断
        $cityCode = $proCode;
        $area = 'sp';
    }
    
    if(!isset($_GET['username'])){
        $username = '捏';
        $username_input = false;
    }else{
        $username = $_GET['username'];
        $username_input = true;
    }
}


// 检查用户名是否为纯中文&日文

if($lang == 'zh'){
    if (!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $username)) {
        echo "Error: 用户名需要纯中文字符。";
        exit();
    }
}


// 用户名为纯中文，输出 IP 地址和用户名
//echo "IP 地址：$ip\n";
//echo "用户名：$username\n";

//echo $cityCode;

$weatherData_DeT = getWeatherInfo ($cityCode);


//print_r($weatherData_DeT);

//if (!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $weatherData_DeT['city'])){
//    echo "天气数据获取失败";
//    exit();
//}

$text = "city" . $weatherData_DeT['city'] . " weather" . $weatherData_DeT['weather'] . " temperature" . $weatherData_DeT['temperature'] . " windpower" . $weatherData_DeT['windpower'] . " winddirection" . $weatherData_DeT['winddirection'] . " humidity" . $weatherData_DeT['humidity'];

// 获取当前时间戳
$timestamp = strtotime($weatherData_DeT["reporttime"]);

// 获取当前小时数
$hour = date('G', $timestamp);

if($lang == 'zh'){
    // 根据小时数定义问候语
    if ($hour >= 6 && $hour < 9) {
        $welcome = "早上好！";
    } elseif ($hour >= 9 && $hour < 12) {
        $welcome = "上午好！";
    } elseif ($hour >= 12 && $hour < 13) {
        $welcome = "中午好！";
    } elseif ($hour >= 13 && $hour < 17) {
        $welcome = "下午好！";
    } elseif ($hour >= 17 && $hour < 19) {
        $welcome = "傍晚了，";
    } elseif ($hour >= 19 && $hour < 23) {
        $welcome = "晚上好！";
    } else {
        $welcome = "半夜了捏，";
    }
}else{
    // Japanese Translation
    if ($hour >= 6 && $hour < 9) {
        $welcome = "おはようございます！";
    } elseif ($hour >= 9 && $hour < 12) {
        $welcome = "おはようございます！";
    } elseif ($hour >= 12 && $hour < 13) {
        $welcome = "こんにちは！";
    } elseif ($hour >= 13 && $hour < 17) {
        $welcome = "こんにちは！";
    } elseif ($hour >= 17 && $hour < 19) {
        $welcome = "こんばんは！";
    } elseif ($hour >= 19 && $hour < 23) {
        $welcome = "こんばんは！";
    } else {
        $welcome = "真夜中ですね，";
    }
}


    
//echo $welcome; // 输出对应的问候语


$GPT_Text = ChatGPT_GEN($text, $lang);
    // 使用正则表达式匹配湿度关键词及其后方的百分比数字和百分号
$pattern = '/湿度(\d+)%/';
$replacement = '湿度为百分之${1}';
$output_text = preg_replace($pattern, $replacement, $GPT_Text);

// 删除百分号
$output_text = str_replace('%', '', $output_text);

// 日文调用谷歌翻译api
if($lang !== 'zh'){
    $output_text = googleTranslation($output_text);
}

$full_text = $welcome . $username . "~ 。" . $output_text;

header('Content-Type: application/json');
$fixedspeaker = preg_replace('/[\r\n\t]+/', '', $speaker);
$fixedspeaker = mb_convert_encoding($fixedspeaker, 'UTF-8');

if($cityCode == '820000' or $cityCode == '810000'){
    $area = 'sp';
}

if ($area !== 'sp'){
    echo Mygo_Vits($full_text, $fixedspeaker, $weatherData_DeT['city'], $lang);
}else{
    echo Mygo_Vits($full_text, $fixedspeaker, $weatherData_DeT['province'], $lang);
}



?>
