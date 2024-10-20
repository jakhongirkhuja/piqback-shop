<?php
namespace App\Helper;

use Illuminate\Support\Facades\DB;

class TempSA	
{
    public static function setSA($tableName, $tableId, $platform, $device, $browser, $timeZone, $connection = 'pgsql')
    {

        DB::connection($connection)->table($tableName)->where('id',$tableId)->update([
            
            'platform'=>$platform,
            'device'=>$device,
            'browser'=>$browser,
            'timeZone'=>$timeZone
        ]);
        
        return true;
    }
}