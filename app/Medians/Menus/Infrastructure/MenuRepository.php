<?php

namespace Medians\Menus\Infrastructure;

use Medians\Menus\Domain\Menu;
use Medians\Pages\Domain\Page;
use Medians\Categories\Domain\Category;
use Medians\Menus\Domain\MenuField;



/**
 * Menu class database queries
 */
class MenuRepository 
{

	/**
	* Find item by `id` 
	*/
	public function find($id) 
	{

		return Menu::find($id);
	}

	/**
	* Find items by `params` 
	*/
	public function get($params = null) 
	{
		return Menu::where('parent_id', 0)->with('items','children', 'page')->get();
	}


	public function getMenuPages($type)
	{
		return Menu::where('type', $type)
		// ->where('parent_id', 0)
		->with('page')
		->whereHas('page', function($q) {
			$q->where('status', 'on');
		})
		->get();
	}


	/**
	* Update item to database
	*/
    public function update($params)
    {
		Menu::where('type', $params['type'])->delete();
		foreach ($params['items'] as $key => $item )
		{
			try {
				$handleType = $this->handleType($item);
				$value = (array) $item;
				$fields = [];
				$fields['name'] = $value['name'];	
				$fields['page_id'] = $handleType['item_id'];	
				$fields['page_type'] = $handleType['item_type'];	
				$fields['parent_id'] = 0;	
				$fields['position'] = $key;	
				$fields['type'] = isset($params['type']) ? $params['type'] : 'header';	
				$Model = Menu::firstOrCreate($fields);

				if (!empty($item->items))
				{
					foreach ($item->items as $subkey => $subitem )
					{
						
						$handleType = $this->handleType($subitem);
						$value = (array) $subitem;
						$fields = [];
						$fields['name'] = $value['name'];	
						$fields['page_id'] = $handleType['item_id'];	
						$fields['page_type'] = $handleType['item_type'];	
						$fields['parent_id'] = $Model->menu_id;	
						$fields['position'] = $subkey;	
						$fields['type'] = isset($params['type']) ? $params['type'] : 'header';	
						$SubModel = Menu::firstOrCreate($fields);
					}
				}

			} catch (\Throwable $th) {
				throw new \Exception($th->getMessage(), 1);
			}
			
		}

		return isset($Model) ? $Model : null;		
    } 

	public function handleType($item)
	{

		if (isset($item->page_id)) {
			return ['item_id' => $item->page_id, 'item_type'=> Page::class];
		}

		if (isset($item->category_id)) {
			return ['item_id' => $item->category_id, 'item_type'=> Category::class];
		}
	} 


	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($id) 
	{
		try {
			
			return Menu::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


	
	/**
	* Save related items to database
	*/
	public function storeItems($data, $id) 
	{
		Menu::where('type', $type)->delete();

		if ($data)
		{

			foreach ($data as $item )
			{
				
				try {
					
					$value = (array) $item;
					if (isset($value['title']) && isset($value['code']))
					{
						$fields = [];
						$fields['menu_id'] = $id;	
						$fields['title'] = $value['title'];	
						$fields['code'] = $value['code'];	
						$fields['type'] = isset($value['type']) ? $value['type'] : 'text';	
						
						$Model = MenuField::firstOrCreate($fields);
					}

				} catch (\Throwable $th) {
					error_log($th->getMessage());
				}
				
			}
	
			return $Model;		
		}
	}
}