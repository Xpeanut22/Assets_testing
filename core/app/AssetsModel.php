<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsModel extends Model
{
	protected $table = 'assets';    


	 public function getStatusAttribute($value)
    {
        return [
        '1' => trans('lang.readytodeploy'),
        '2' => trans('lang.pending'),
        '3' => trans('lang.archived'),
        '4' => trans('lang.broken'),
        '5' => trans('lang.lost'),
        '6' => trans('lang.outofrepair')
        ][$value];
    }

}
