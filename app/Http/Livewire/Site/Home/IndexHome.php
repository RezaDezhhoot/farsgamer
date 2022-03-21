<?php

namespace App\Http\Livewire\Site\Home;

use App\Helper\Helper;
use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use App\Models\Platform;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use App\Models\Setting;
use App\Models\Order;

class IndexHome extends BaseComponent
{
    public $content  , $data , $q , $max , $min = 0 , $view , $category = [] , $platform, $platforms , $original_categories;
    public $logo , $contact , $title , $most_categories;
    protected $queryString = ['view','q' ,'platform'];
    public function mount()
    {
        SEOMeta::setTitle(Setting::getSingleRow('title'),false);
        SEOMeta::setDescription(Setting::getSingleRow('seoDescription'));
        SEOMeta::addKeyword(Setting::getSingleRow('seoKeyword'));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle(Setting::getSingleRow('title'));
        OpenGraph::setDescription(Setting::getSingleRow('seoDescription'));
        TwitterCard::setTitle(Setting::getSingleRow('title'));
        TwitterCard::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::setTitle(Setting::getSingleRow('title'));
        JsonLd::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::addImage(Setting::getSingleRow('logo'));
        $this->most_categories = Category::withCount('orders')->active()
            ->take(Setting::getSingleRow('categoryHomeCount') ?? 120)->get()->sortByDesc('order_count');

        $categories = Category::with(['childrenRecursive'])->whereNull('parent_id')->active()->get()->toArray();
        $original_categories = [];
        $i = 0;
        foreach ($categories as $category){
            if ($i >= Setting::getSingleRow('categoryHomeCount')) break;

            $sub_categories_id = $this->array_value_recursive('id',$category['children_recursive']);
            $sub_categories =  Category::active()->findMany($sub_categories_id);
            $original_categories[$i] = $category;
            $original_categories[$i]['sub_categories'] = $sub_categories;
            $i++;
        }
        $this->platforms = Platform::all()->pluck('slug');
        $this->logo = Setting::getSingleRow('logo');
        $this->title = Setting::getSingleRow('title');
        $this->contact = Setting::getSingleRow('contact');
        $this->original_categories = $original_categories;
    }
    public function render()
    {
        $orders = Order::where('status',Order::IS_CONFIRMED);
        $this->min = $orders->min('price');
        $this->max = $orders->max('price');
        $orders = $orders->with(['category','platforms','parameters'])
            ->where([
                ['price','=<',$this->max],
                ['price','=>',$this->min],
            ])->when($this->q,function ($query){
                return $query->whereHas('category',function ($query) {
                    return $query->where('slug','LIKE','%'.$this->q.'%')->orWhere('title','LIKE','%'.$this->q.'%');
                })->orWhereHas('platforms',function ($query){
                    return $query->where('slug','LIKE','%'.$this->q.'%');
                });
            })->when($this->category,function ($query) {
                return $query->whereIn('category_id',$this->category);
            })->when($this->platform,function ($query){
                return $query->whereHas('platforms',function ($query) {
                    return $query->where('slug',$this->platform);
                });
            })->active();
        $orders = $orders->orderBy($this->view == 1 ? 'view_count' : 'id','desc');
        $this->data['orders'] = $orders->get();
        return view('livewire.site.home.index-home')
            ->extends('livewire.site.layouts.site.site');
    }

    public function setCategory($id)
    {
        if (in_array($id,$this->category))
            unset($this->category[array_search($id,$this->category)]);
        else array_push($this->category,$id);
    }
}
