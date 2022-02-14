<?php

namespace App\Traits\Admin;
use App\Models\Setting;
use App\Models\Task;
use Morilog\Jalali\Jalalian;

trait TextBuilder
{
    public function createText($where,$model)
    {
        $text = [];
        $codes = Setting::codes();
        $tasks = Task::where('where',$where)->get();
        if (!is_null($tasks)) {
            foreach ($tasks as $item) {
                $text[$item->task][$item->id] = $item->value;
                $text = $this->setText($item , $text , $codes , $model);
            }
        }
       return $text;
    }

    public function setText($item , $text , $codes , $model)
    {
        foreach ($codes as $key => $value) {
            if (str_contains($text[$item->task][$item->id], $key)){
                $table = trim(explode('_',$key)[0],'{');
                $column = trim(str_replace('{'.$table.'_','',$key),'}');
                if ($key == '{date}')
                    $text[$item->task][$item->id] = str_replace($key,
                        Jalalian::now()->format('Y/m/d'),$text[$item->task][$item->id]);
                elseif ($key == '{time}')
                    $text[$item->task][$item->id] = str_replace($key,
                        Jalalian::now()->format('H:i'),$text[$item->task][$item->id]);
                if (method_exists($model,$table) && in_array($table,['customer','seller'])){
                    $text[$item->task][$item->id] = str_replace($key,$model->{$table}->{$column},$text[$item->task][$item->id]);
                    continue;
                }
                if (method_exists($model,'getTable') && self::pluralize($table) == $model->getTable()) {
                    if ($column == 'timer')
                        $text[$item->task][$item->id] = str_replace($key,$model->{$column}->diffForHumans(),$text[$item->task][$item->id]);
                    elseif ($column == 'ban')
                        $text[$item->task][$item->id] = str_replace($key,
                            Jalalian::forge($model->{$column})->format('Y/m/d H:i'),$text[$item->task][$item->id]);
                    elseif ($column == 'status') {
                        $col = '';
                        if (method_exists($model,"getStatus"))
                            $col = $model::getStatus()[$model->{$column}];

                        $text[$item->task][$item->id] = str_replace($key,$col,$text[$item->task][$item->id]);
                    } else
                        $text[$item->task][$item->id] = str_replace($key,$model->{$column},$text[$item->task][$item->id]);

                } elseif (method_exists($model,$table) && !is_null($model->{$table} )){
                    $text = $this->setText($item , $text ,$codes , $model->{$table});
                }
            }
        }
        return $text;
    }

    static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])um$/i'             => "$1a",
        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias)$/i'              => "$1es",
        '/(octop)us$/i'            => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );

    static $singular = array(
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
        '/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias)es$/i'             => "$1",
        '/(octop|vir)i$/i'          => "$1us",
        '/(cris|ax|test)es$/i'      => "$1is",
        '/(shoe)s$/i'               => "$1",
        '/(o)es$/i'                 => "$1",
        '/(bus)es$/i'               => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
        '/(m)ovies$/i'              => "$1ovie",
        '/(s)eries$/i'              => "$1eries",
        '/([^aeiouy]|qu)ies$/i'     => "$1y",
        '/([lr])ves$/i'             => "$1f",
        '/(tive)s$/i'               => "$1",
        '/(hive)s$/i'               => "$1",
        '/(li|wi|kni)ves$/i'        => "$1fe",
        '/(shea|loa|lea|thie)ves$/i'=> "$1f",
        '/(^analy)ses$/i'           => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
        '/([ti])a$/i'               => "$1um",
        '/(n)ews$/i'                => "$1ews",
        '/(h|bl)ouses$/i'           => "$1ouse",
        '/(corpse)s$/i'             => "$1",
        '/(us)es$/i'                => "$1",
        '/s$/i'                     => ""
    );

    static $irregular = array(
        'move'   => 'moves',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people',
        'valve'  => 'valves',
        'request'  => 'requests',
        'orderTransaction' => 'orders_transactions',
    );

    static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );

    public static function pluralize( $string )
    {
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        foreach ( self::$irregular as $pattern => $result )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        foreach ( self::$plural as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

    public static function singularize( $string )
    {
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        foreach ( self::$irregular as $result => $pattern )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        foreach ( self::$singular as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

    public static function pluralize_if($count, $string)
    {
        if ($count == 1)
            return "1 $string";
        else
            return $count . " " . self::pluralize($string);
    }
}
