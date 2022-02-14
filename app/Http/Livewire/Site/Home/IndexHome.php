<?php

namespace App\Http\Livewire\Site\Home;

use App\Helper\Helper;
use App\Http\Livewire\BaseComponent;
use App\Models\Category;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use App\Models\Setting;
use App\Models\Order;

class IndexHome extends BaseComponent
{
    public $content  , $data , $q , $max , $min = 0 , $view , $category , $platform;
    protected $queryString = ['view','q','min','max' ,'category','platform'];
    public function mount()
    {
        SEOMeta::setTitle(Setting::getSingleRow('title'),false);
        SEOMeta::setDescription(Setting::getSingleRow('seoDescription'));
        SEOMeta::addKeyword(explode(',',Setting::getSingleRow('seoKeyword')));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle(Setting::getSingleRow('title'));
        OpenGraph::setDescription(Setting::getSingleRow('seoDescription'));
        TwitterCard::setTitle(Setting::getSingleRow('title'));
        TwitterCard::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::setTitle(Setting::getSingleRow('title'));
        JsonLd::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::addImage(Setting::getSingleRow('logo'));
        $this->data['categories'] = Category::where('status',Category::AVAILABLE)->withCount('orders')
            ->take(Setting::getSingleRow('categoryHomeCount') ?? 10)->get()->sortByDesc('order_count');
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
                $data = Category::with(['childrenRecursive'])->where('slug', $this->category)->get()->toArray();
                return $query->whereIn('category_id',Helper::array_value_recursive('id',$data));
            })->when($this->platform,function ($query){
                return $query->whereHas('platforms',function ($query) {
                    return $query->wehre('slug',$this->platform);
                });
            });
        $orders = $orders->orderBy($this->view == 1 ? 'view_count' : 'id','desc');
        $this->data['orders'] = $orders->get();
        return view('livewire.site.home.index-home');
    }
}
