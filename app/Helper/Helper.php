<?php


namespace App\Helper;


class Helper
{
    public static function get_subcategories($array, $parent_id)
    {
        $return = array();
        foreach ($array as $key => $category)
        {
            if ($category->parent_id == $parent_id)
            {
                $category->sub_categories = self::get_subcategories($array, $category->id);
                $return[] = $category;
            }
        }
        return $return;
    }
    public static function print_categories($array, $level = 0 )
    {
        foreach ($array as $category){
            ?>
            <option style="<?php ($category->parent_id == 0 ? print('font-weight:bold') : '') ?>" value="<?php echo $category->id ?>">
                <?php echo $category->title ?>
            </option>
            <?php
            self::print_categories($category->sub_categories, ++$level);
        }
    }

    public static function array_value_recursive($key, array $arr){
        $val = array();
        array_walk_recursive($arr, function($v, $k) use($key, &$val){
            if($k == $key) array_push($val, $v);
        });
        return $val;
    }

}
