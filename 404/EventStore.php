<?php
//class EventStore {
//    private static $file = __DIR__."/events.json";
//
//    public static function push(array $event){
//        $all = self::all();
//        $all[] = $event;
//        file_put_contents(self::$file, json_encode($all));
//    }
//
//    public static function all(){
//        if(!file_exists(self::$file)) return [];
//        return json_decode(file_get_contents(self::$file), true);
//    }
//
//    public static function latest(){
//        $all = self::all();
//        return end($all);
//    }
//}
//?>