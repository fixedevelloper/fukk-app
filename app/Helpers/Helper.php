<?php


namespace App\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;


class Helper
{
    public static function extractIDS($values){
        $ids=[];
        for ($i = 0; $i < sizeof($values); ++$i) {
            $ids[]=$values[$i]['value'];
        }
        return json_encode($ids);
    }
    public static function dayAgo($from){
        if($from==null){ return '-'; }
        date_default_timezone_set('UTC');
        $diff_time= Carbon::createFromTimeStamp(strtotime($from))->diffForHumans();
        return $diff_time;
    }
    public static function str_slug($text){
        $STEXT=str_replace('/','',$text);
        $STEXT=preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '_', $STEXT);
        return strtolower(str_ireplace(" ","_",$STEXT)) ;
    }
    public static function slug($data){
        $slug=str_ireplace(" ","_",strtolower($data));
        return $slug;
    }
    public static function getGainParrainage($user_id){
        $payments=Payment::query()->where(['user_id'=>$user_id,'status'=>'success'])->get();
        return sizeof($payments)*env('PRICE_PARRAIN');
    }
    public static function generatealeatoire($size){
        $allowed_characters = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0,"a","z","e","r","t","y","u","i","o"
            ,"p","q","s","d","f","g","h","j","k","l","m","w","x","c","v","b","n"];
        $all="";
        for ($i = 1; $i <= intval($size); ++$i) {
            $all .= $allowed_characters[rand(0, count($allowed_characters) - 1)];
        }
        return $all;
    }
    public static function generatealeatoireNumeric($size){
        $allowed_characters = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
        $all="";
        for ($i = 1; $i <= intval($size); ++$i) {
            $all .= $allowed_characters[rand(0, count($allowed_characters) - 1)];
        }
        return $all;
    }
    public static function send_contact($data)
    {
        //logger(env('MAIL_FROM_ADDRESS'));
        $data_ = array('email' => $data['email'],
            'name' => $data['name'],'subject' => 'contact form','data' => $data['message']);
        Mail::send(['html' => 'mails.contact'], $data_, function ($message)
        use ($data) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->to($data['email'], $data['name'])->subject("Contact form");
                  });

    }
    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
        return [
            'success' => 1,
            'message' => 'Removed successfully !'
        ];
    }

    public static function mailajet($data){
        $mj = Mailjet::getClient();

        $body = [
            'FromEmail' => 'contact@guens-education.com',
            'FromName' => env('MAIL_FROM_NAME'),
            'Subject' => "contact form",
            'MJ-TemplateID' => 6207309,
            'MJ-TemplateLanguage' => true,
            'Vars' => json_decode(json_encode($data), true),
            'Recipients' => [['Email' => $data['email'],'infos@guens-education.com']]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        if($response->success()){
          return 1;
    } else {
        return 0;
    }
    }
}
