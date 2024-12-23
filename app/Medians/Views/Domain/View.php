<?php

namespace Medians\Views\Domain;


use Shared\dbaser\CustomModel;

class View extends CustomModel
{


	/*
	/ @var String
	*/
	protected $table = 'views';


	protected $fillable = [
    	'item_type',
    	'item_id',
    	'session',
    	'times',
    	'date',
	];

	public $appends = ['class'];

	public function getClassAttribute()
	{
		$itemType = explode("\\", $this->item_type);
		return $itemType ? end($itemType) : '';
	}

	public function item()
	{
		return $this->morphTo();
	}

	public static function itemViews($item, $item_id, $start, $end)
	{
		return View::where('item_type', get_class($item))->where('item_id', $item_id)->whereBetween('date', [$start, $end])->count();
	}

	public static function totalViews($start, $end)
	{
		return View::whereBetween('date', [$start, $end]);
	}

}
