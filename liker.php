<?php
error_reporting(0);
set_time_limit(0);
ignore_user_abort(1);
echo "SGB Instagram Liker\n";
echo "Original Web Based script by Ardy Muhammad Johan Syah\n";
echo "NB : Web Based to CLI - Script Recode by Wahyu AP. \n";
    function proccess($ighost, $useragent, $url, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0){
        $url = $ighost ? 'https://i.instagram.com/api/v1/' . $url : $url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        if($proxy):
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        endif;
        if($httpheader) curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        if ($data):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        endif;
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch);
        if(!$httpcode) return false; else{
            $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            curl_close($ch);
            return array($header, $body);
        }
    }
    function generate_useragent($sign_version = '6.22.0'){
        $resolusi = array('1080x1776','1080x1920','720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
        $versi = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');     $dpi = array('120', '160', '320', '240');
        $ver = $versi[array_rand($versi)];
        return 'Instagram '.$sign_version.' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi[array_rand($dpi)].'; '.$resolusi[array_rand($resolusi)].'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
    }
    function hook($data) {
        return 'ig_sig_key_version=4&signed_body=' . hash_hmac('sha256', $data, '469862b7e45f078550a0db3687f51ef03005573121a3a7e8d7f43eddb3584a36') . '.' . urlencode($data); 
    }
    function generate_device_id(){
        return 'android-' . md5(rand(1000, 9999)).rand(2, 9);
    }
    function generate_guid($tipe = 0){
        $guid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 65535), 
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(16384, 20479), 
        mt_rand(32768, 49151),
        mt_rand(0, 65535), 
        mt_rand(0, 65535), 
        mt_rand(0, 65535));
        return $tipe ? $guid : str_replace('-', '', $guid);
    }
echo "\nUsername        : ";
$user = trim(fgets(STDIN, 1024)); 
echo "\nPassword      : ";
$pass = trim(fgets(STDIN, 1024));
        $ua = generate_useragent();
        $devid = generate_device_id();
        $login = proccess(1, $ua, 'accounts/login/', 0, hook('{"device_id":"'.$devid.'","guid":"'.generate_guid().'","username":"'.$user.'","password":"'.$pass.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'));
        $data = json_decode($login[1]);
        if($data->status<>ok)
            echo "GAGAL LOGIN \n";
        else{
            preg_match_all('%Set-Cookie: (.*?);%',$login[0],$d);$cookie = '';
            for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
            $hand=fopen('sgbteam.lst','a');
            fwrite($hand, json_encode(array('cookies' => $cookie, 'useragent' => $ua, 'device_id' => $devid, 'username' => $data->logged_in_user->username, 'id' => $data->logged_in_user->pk))."\n");
            fclose($hand);
           echo "SUKSES LOGIN \n";       
}
$text = "WoW";
$listaccounts = explode("\n", file_get_contents('sgbteam.lst'));
foreach($listaccounts as $account):
$data_session = json_decode($account);
if($account!==''):
 
$getinfo = proccess(1, $data_session->useragent, 'feed/timeline', $data_session->cookies);
$dil = json_decode($getinfo[1]);

$follow = proccess(1, $data_session->useragent, 'friendships/create/1296839350/', $data_session->cookies, hook('{"user_id":"1296839350"}'));
$foll = json_decode($follow[1]);
$follow = proccess(1, $data_session->useragent, 'friendships/create/1296839350/', $data_session->cookies, hook('{"user_id":"1296839350"}'));
$foll = json_decode($follow[1]);
foreach ($dil->items as $item) {
$lampu = $item->id;    
$nama = $item->user->username;
$cross = proccess(1, $data_session->useragent, 'media/'.$lampu.'/like/', $data_session->cookies, hook('{"like_text":"'.$text.'"}'));
    $cross = json_decode($cross[1]);
echo "[SUKSES LIKE => ".$nama."]\n";
}
endif; endforeach; ?>