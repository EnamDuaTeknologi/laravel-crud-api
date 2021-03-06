<?php

namespace EnamDuaTeknologi\LaravelCrudApi\Transformers\v1\Crud;

use EnamDuaTeknologi\LaravelCrudApi\Models\Crud;

class FieldTransformer
{
    public static function transform($array, $table)
    {
        return array_map(function($i) use($table) {
            $type = explode(" ", str_replace(["(", ")"], " ", $i->Type));

            $return = [
                'field' => $i->Field,
                'type' => $type[0]
            ];

            if($i->Field == 'parent_id') {
                $return['type'] = 'relation';

                $return['relation'] = [
                    'field' => str_replace('_id', '', $i->Field),
                    'data' => (new Crud())->setTable($table)
                        ->select('id', 'code', 'description')
                        ->get()
                ];
            } elseif($i->Field == 'password') {
                $return['type'] = $i->Field;
                $return['table_hidden'] = true;
            } elseif($i->Field == 'image' || strpos($i->Field, '_image')) {
                $return['type'] = 'image';
                $return['table_hidden'] = true;
            } elseif(strpos($i->Field, '_id')) {
                $return['type'] = 'relation';

                $return['relation'] = [
                    'field' => str_replace('_id', '', $i->Field),
                    'data' => (new Crud())->setTable(str_replace('_id', '', $i->Field).'s')
                        ->select('id', 'code', 'description')
                        ->get()
                ];
            }

            if(isset($type[1])) $return['length'] = $type[1];

            if($i->Default) $return['default'] = $i->Default;

            if($i->Null != "YES") $return['required'] = true;

            if($i->Key == 'PRI' || $i->Field == 'created_at') $return['form_hidden']
                = $return['table_hidden']
                = true;

            if($i->Field == 'updated_at') $return['form_hidden'] = true;


            return $return;
        }, $array);
    }
}
