<?php


use JetBrains\PhpStorm\Pure;

class attendanceController
{
    private dbController $db;

    public function __construct()
    {
        $this->db = new dbController();
    }


    public function curl($url): bool|string
    {

        $curl=curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

        $result=curl_exec($curl);
        $result=mb_convert_encoding($result,'UTF-8','UTF-16LE');
        curl_close($curl);

      return $result;
    }

    public function toCSV($string): array
    {
        $csv=[];
        $lines=explode(PHP_EOL,$string);
        foreach ($lines as $line) {
            $csv[]=str_getcsv($line,"\t");
        }

        return $csv;
    }
    public function formatNames($list_names): array
    {
        $result=[];
        foreach ($list_names as $names){
            foreach ($names as $name){
                $result[]= $name;
            }

        }
        return $result;
    }

    public function getNames(): array
    {

        $result=$this->db->getTables(DB_NAME);
        $names=[];

        foreach ($result as $tables){
            foreach ($tables as $table){
                $names[]= $this->db->getNames($table);
            }

        }

        return array_unique($this->formatNames($names));

    }
    private function formatDate($table_dates): array
    {
        $result=[];
        foreach ($table_dates as $datetime){
            foreach ($datetime as $date){
                $result[]=substr($date,0,10);
            }
        }
        return $result;
    }

    public function getDates(): array
    {
        $result=$this->db->getTables(DB_NAME);
        $table_dates=[];
        foreach ($result as $tables) {
            foreach ($tables as $table) {
                $table_dates[]= $this->db->getDate($table);
            }
        }

        return array_unique($this->formatDate($table_dates));
    }




    public function getTime($name,$table): string
    {


        $result=$this->db->getActivity($name,$table);
        $active=$this->db->getDetailActivity($name,$table);
        if($result==array()){
            return "00:00";
        }
        $times=[];

        foreach ($result as $datetime){
            $times[]=new DateTime($datetime);

        }
        if(count($times)%2!=0){
            if(strpos($active[count($active)-1],"d") ){

                $date=$this->db->getLastDate($table)[0];
                $date=new DateTime($date);
                $time=$times[0]->diff($date)->format("%H:%I:%S");
                list($hours, $minutes, $seconds) = explode(':', $time);
                $m=intval($hours)*60;
                $minutes=intval($minutes);
                $minutes+=$m;
                return sprintf('%02d:%02d', $minutes,$seconds)."c";
            }

        }
        $time=$times[0]->diff($times[count($times)-1])->format("%H:%I:%S");
        list($hours, $minutes, $seconds) = explode(':', $time);
        $m=intval($hours)*60;
        $minutes=intval($minutes);
        $minutes+=$m;
        return sprintf('%02d:%02d', $minutes,$seconds);
    }

    public function getTables(): array
    {
        $tbl=$this->db->getTables(DB_NAME);
        $result=[];
        foreach ($tbl as $tables){
            foreach ($tables as $table){
                if(!array_search($table,$result))
                    $result[]=$table;
            }
        }
        return array_unique($result);
    }
    #[Pure] public function getTotalTime($name): string
    {
        $all_seconds=0;
        $tbl=$this->getTables();

        foreach ($tbl as $table){
            $time=$this->getTime($name,$table);
            list( $minute, $second) = explode(':', $time);
            $all_seconds += intval($minute) * 3600;
            $all_seconds += intval($second) * 60;

        }



        $total_minutes = floor($all_seconds/60);
        $hours = floor($total_minutes / 60);
        $minutes = $total_minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);

    }

    public function getAttendance($name):string
    {
        $attendance=0;
        $missing="00:00";
        $tbl=$this->getTables();
        foreach ($tbl as $tables){
            if($this->getTime($name,$tables)!=$missing)
                $attendance++;
        }

        return $attendance;
    }
    public function getDetailedActivity($name,$table): string
    {
        $result=$this->db->getDetailActivity($name,$table);

        if($result==array()){
            return "Osoba-sa-tejto-prednášky-nezúčastnila-";
        }

        $activities='';

        foreach ($result as $activity){

            if(strpos($activity,"d"))
                $activities.="XJoined:-".trim(substr($activity,strpos($activity," "),strlen($activity)));


            else if(strpos($activity,"t"))
                $activities.="XLeft:-".trim(substr($activity,strpos($activity," "),strlen($activity)));


        }
        return $activities;
    }
    public function getCountUser($table): ?string
    {
        return $this->db->getCountPerson($table)[0];
    }
    public function getLastDate($table):string
    {
        return substr($this->db->getLastDate($table)[0],0,10);
    }





}