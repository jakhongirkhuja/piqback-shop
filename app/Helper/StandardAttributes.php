<?php
namespace App\Helper;

use App\Models\Phonebook;
use Illuminate\Support\Facades\DB;

class StandardAttributes	
{
    public static function setSA($tableName, $tableId, $status, $addressIP, $platform, $device, $browser, $timeZone, $connection = 'pgsql')
    {

        DB::connection($connection)->table($tableName)->where('id',$tableId)->update([
            'status'=>$status,
            'addressIP'=>$addressIP,
            'platform'=>$platform,
            'device'=>$device,
            'browser'=>$browser,
            'timeZone'=>$timeZone
        ]);
        
        return true;
    }
}