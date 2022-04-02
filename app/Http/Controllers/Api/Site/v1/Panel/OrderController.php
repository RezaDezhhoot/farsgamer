<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\OrderCollection;
use App\Http\Resources\v1\Order;
use App\Http\Resources\v1\PlatformCollection;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ParameterRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{

    private $categoryRepository, $orderRepository , $settingRepository , $parameterRepository;
    public function __construct(
        CategoryRepositoryInterface $categoryRepository ,
        OrderRepositoryInterface $orderRepository ,
        SettingRepositoryInterface $settingRepository ,
        ParameterRepositoryInterface $parameterRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->orderRepository = $orderRepository;
        $this->settingRepository = $settingRepository;
        $this->parameterRepository = $parameterRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = $this->orderRepository->getUserOrders(Auth::user(),$request);
        return response([
            'data' => [
                'orders' => [
                    'records' => new OrderCollection($orders),
                    'paginate' => [
                        'total' => $orders->total(),
                        'count' => $orders->count(),
                        'per_page' => $orders->perPage(),
                        'current_page' => $orders->currentPage(),
                        'total_pages' => $orders->lastPage()
                    ]
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'price' => ['required','numeric','between:0,999999999999999999999999999999999999999999999.99999999999'],
            'category_id' => ['required','exists:categories,id'],
        ],[],[
            'price' => 'هزینه',
            'category_id' => 'دسته بندی',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $price = $this->calculateCommission($request['price'],$this->categoryRepository->find($request['category_id'],true,true));
        return response([
            'data' =>  [
                'result' => [
                    'price' => $request['price'],
                    'intermediary' => $price['intermediary']/2,
                    'commission' => $price['commission']/2,
                    'total' => $request['price'] - $price['commission']/2 - $price['intermediary']/2,
                ]
            ],
            'status' => 'error'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function details()
    {
        return response([
            'data' => [
                'details' => [
                    'categories' => [
                        'digital' => $this->categoryRepository->getCategories($this->categoryRepository::digital())->map(function ($item){
                            return [
                                'id' => $item->id,
                                'title' => $item->title,
                                'logo' => asset($item->logo),
                                'commission' => round($item->commission/2,3),
                                'commission_type' => 'percent',
                                'need_intermediary' => $item->control,
                                'intermediary' => round($item->intermediary/2,3),
                                'intermediary_type' => 'percent',
                                'platforms' => new PlatformCollection($item->platforms),
                                'parameters' => $item->parameters->map(function ($item){
                                    return [
                                        'id' => $item->id,
                                        'logo' => asset($item->logo),
                                        'name' => $item->name,
                                        'placeholder' => $item->field,
                                        'type' => $item->type,
                                        'min' => $item->min,
                                        'max' => $item->max,
                                        'required' => true,
                                    ];
                                }),

                            ];
                        }),
                        'physical' => $this->categoryRepository->getCategories($this->categoryRepository::physical())
                    ],
                    'max_gallery_images_count' => $this->settingRepository->getSiteFaq('order_images_count'),
                    'min_gallery_images_count' => 0,
                    'valid_image_mimes' => $this->settingRepository->getSiteFaq('valid_order_images'),
                    'provinces'=> $this->settingRepository::getProvince(),
                    'cities' => $this->settingRepository->getCities(),
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rateKey = 'order:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 25)) {
            return
                response([
                    'data' => [
                        'message' => [
                            'user' => ['زیادی تلاش کردی لطفا پس از مدتی دوباره سعی کنید.']
                        ]
                    ],
                    'status' => 'error'
                ],Response::HTTP_TOO_MANY_REQUESTS);
        }
        RateLimiter::hit($rateKey, 24 * 60 * 60);
        if ($request->has('category_id'))
            $category = $this->categoryRepository->findNormal($request['category_id'],true,true);
        else return response([
            'data' => [
                'message' => [
                    'category_id' => ['فیلد دسته بندی الزامی می باشد'],
                ]
            ], 'status' => 'error'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);

        $platforms = $category->platforms->pluck('id')->toArray();
        $parameters = $category->parameters;
        $fields = [
            'name' => ['required','string','max:55'],
            'content' => ['required','string','max:4200'],
            'price' => ['required','numeric', 'between:0,9999999999999999999999999999.99999'],
            'image' => ['required','mimes:'.$this->settingRepository->getSiteFaq('valid_order_images'),'max:'.$this->settingRepository->getSiteFaq('max_order_image_size')],
            'gallery' => ['array','min:0','max:'.$this->settingRepository->getSiteFaq('order_images_count')],
            'gallery.*' => ['required','mimes:'.$this->settingRepository->getSiteFaq('valid_order_images'),'max:'.$this->settingRepository->getSiteFaq('max_order_image_size')],
            'platforms' => ['array'],
            'platforms.*' => ['required','in:'.implode(',',$platforms)],
            'parameters' => ['array','size:'.count($parameters->pluck('id')->toArray())],
            'parameters.*.id' => ['required','in:'.implode(',',$parameters->pluck('id')->toArray())],
            'parameters.*.value' => ['required','max:255'],
        ];
        $messages = [
            'name' => 'نام',
            'content' => 'توضیحات',
            'price' => 'هزینه',
            'image' => 'تصویر',
            'gallery' => 'تصاویر',
            'gallery.*' => 'تصاویر',
            'platforms' => 'پلتفرم ها',
            'platforms.*' => 'پلتفرم',
            'parameters' => 'پارامتر ها',
            'parameters.*.id' => 'پارامتر',
            'parameters.*.value' => 'مقدار پارامتر',
        ];
        $types = [
            'number' => 'numeric',
            'text' => 'string',
        ];
        $params = [];
        if ($request->has('parameters') && gettype($request['parameters']) == 'array'){
            foreach ($request['parameters'] as $key => $value) {
                $param = $this->parameterRepository->find($value['id']);
                $max = $param->max;
                $min = $param->min;
                $type = $param->type;
                $fields['parameters.'.$key.'.value'] = ['required','min:'.(empty($min) ? 0 : $min),'max:'.(empty($max) ? 255 : $max),$types[$type]];
                $params[] = [
                    'parameter_id' => $value['id'],
                    'value' => $value['value'],
                ];
            }
        }
        if ($category->type == $this->categoryRepository::physical()){
            if (!isset($request['province']) || empty($request['province']) || !in_array($request['province']
                    ,array_keys($this->settingRepository::getProvince()))) {
                return response([
                    'data' => [
                        'message' => [
                            'province' => ['فیلد استان الزامی می باشد'],
                        ]
                    ], 'status' => 'error'
                ],Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $fields['province'] = ['required','max:150','in:'.implode(',',array_keys($this->settingRepository::getProvince()))];
            $fields['city'] = ['required','max:150','in:'.implode(',',array_keys($this->settingRepository->getCity($request['province'])))];
            $messages['province'] = 'استان';
            $messages['city'] = 'شهر';
        }
        $validator = Validator::make($request->all(),$fields,[],$messages);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ], 'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $imagePath = "app/public/orders";
        $gallery = [];
        $image = '';
        if (!empty($request->file('image'))){
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $imageName = Carbon::now()->timestamp.'-'.$imageName;
            $image->move(storage_path($imagePath),$imageName);
            $image = "storage/orders/{$imageName}";
            $this->imageWatermark($image);
        }
        if (!empty($request->file('gallery'))){
            $file = $request->file('gallery');

            foreach ($file as $item)
            {
                $fileName = $item->getClientOriginalName();
                $fileName = Carbon::now()->timestamp.'-'.$fileName;

                $item->move(storage_path($imagePath),$fileName);
                $files = "storage/orders/{$fileName}";
                $this->imageWatermark($files,'center');
                $gallery[] = $files;
            }
        }

        $order = [
            'slug' => $request['name'],
            'category_id' => $category->id,
            'status' => $this->orderRepository::isNewStatus(),
            'content' => $request['content'],
            'price' => $request['price'],
            'image' => $image,
            'gallery' => implode(',',$gallery),
            'province' => $request['province'] ?? null,
            'city' => $request['city'] ?? null,
        ];
        try {
            DB::beginTransaction();
            $order = $this->orderRepository->create(Auth::user(),$order);
            $this->orderRepository->attachPlatforms($order,$request['platforms']);
            $this->orderRepository->attachParameters($order,$params);
            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
            return response([
                'data' =>  [
                    'message' => [
                        'order' => ['خظار در ثبت اگهی']
                    ]
                ], 'status' => 'error'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response([
            'data' => [
                'order' => [
                    'record' => new Order($order)
                ],
                'message' => [
                    'order' => ['اگهی با موفقیت ثبت شد']
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response([
            'data' => [
                'order' => [
                    'record' => new Order($this->orderRepository->getUserOrder(Auth::user(),$id))
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = $this->orderRepository->getUserOrder(Auth::user(),$id);
        if (!in_array($order->status,[$this->orderRepository::isNewStatus(),$this->orderRepository::isConfirmedStatus()])){
            return response([
                'data' => [
                    'message' => [
                        'order' => 'برای این اگهی امکان حذف وجود ندارد.'
                    ]
                ],'status' => 'error'
            ],Response::HTTP_FORBIDDEN);
        }
        if ($request->has('category_id'))
            $category = $this->categoryRepository->findNormal($request['category_id'],true,true);
        else return response([
            'data' => [
                'message' => [
                    'category_id' => ['فیلد دسته بندی الزامی می باشد'],
                ]
            ], 'status' => 'error'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
        $platforms = $category->platforms->pluck('id')->toArray();
        $parameters = $category->parameters;
        $old_gallery = explode(',',$order->gallery);
        $fields = [
            'name' => ['required','string','max:55'],
            'content' => ['required','string','max:4200'],
            'price' => ['required','numeric', 'between:0,9999999999999999999999999999.99999'],
            'image' => ['required'],
            'gallery' => ['array'],
            'gallery.*' => ['required','min:0'],
            'platforms' => ['array'],
            'platforms.*' => ['required','in:'.implode(',',$platforms)],
            'parameters' => ['array','size:'.count($parameters->pluck('id')->toArray())],
            'parameters.*.id' => ['required','in:'.implode(',',$parameters->pluck('id')->toArray())],
            'parameters.*.value' => ['required','max:255'],
        ];
        $messages = [
            'name' => 'نام',
            'content' => 'توضیحات',
            'price' => 'هزینه',
            'image' => 'تصویر',
            'gallery' => 'تصاویر',
            'gallery.*' => 'تصاویر',
            'platforms' => 'پلتفرم ها',
            'platforms.*' => 'پلتفرم',
            'parameters' => 'پارامتر ها',
            'parameters.*.id' => 'پارامتر',
            'parameters.*.value' => 'مقدار پارامتر',
        ];
        $types = [
            'number' => 'numeric',
            'text' => 'string',
        ];
        $params = [];
        foreach ($request['parameters'] as $key => $value) {
            $param = $this->parameterRepository->find($value['id']);
            $max = $param->max;
            $min = $param->min;
            $type = $param->type;
            $fields['parameters.'.$key.'.value'] = ['required','min:'.(empty($min) ? 0 : $min),'max:'.(empty($max) ? 255 : $max),$types[$type]];
            $params[] = [
                'parameter_id' => $value['id'],
                'value' => $value['value'],
            ];
        }
        if ($category->type == $this->categoryRepository::physical()){
            if (!isset($request['province']) || empty($request['province']) || !in_array($request['province']
                    ,array_keys($this->settingRepository::getProvince()))) {
                return response([
                    'data' => [
                        'message' => [
                            'province' => ['فیلد استان الزامی می باشد'],
                        ]
                    ], 'status' => 'error'
                ],Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $fields['province'] = ['required','max:150','in:'.implode(',',array_keys($this->settingRepository::getProvince()))];
            $fields['city'] = ['required','max:150','in:'.implode(',',array_keys($this->settingRepository->getCity($request['province'])))];
            $messages['province'] = 'استان';
            $messages['city'] = 'شهر';
        }
        if (!empty($request->file('gallery')) || count($old_gallery) < 1){
            $fields['gallery'] = ['array','min:0','max:'.($this->settingRepository->getSiteFaq('order_images_count') - count($old_gallery))];
            $fields['gallery.*'] = ['required','mimes:'.$this->settingRepository->getSiteFaq('valid_order_images'),'max:'.$this->settingRepository->getSiteFaq('max_order_image_size')];
        }
        $validator = Validator::make($request->all(),$fields,[],$messages);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ], 'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $imagePath = "app/public/orders";
        if (!empty($request->file('image'))){
            $image = $request->file('image');
            if (in_array(str_replace('image/','',$image->getMimeType()),explode(',',$this->settingRepository->getSiteFaq('valid_order_images')))){
                if (floor($image->getSize()/1024) <= (int)$this->settingRepository->getSiteFaq('max_order_image_size')){
                    $imageName = $image->getClientOriginalName();
                    $imageName = Carbon::now()->timestamp.'-'.$imageName;
                    $image->move(storage_path($imagePath),$imageName);
                    $image = "storage/orders/{$imageName}";
                    $this->imageWatermark($image);
                    if (!is_null($order->image)) @unlink($order->image);

                } else return response([
                    'data' => [
                        'message' => [
                            'image' => ['حجم تصویر بیش از حد مجاز'],
                        ]
                    ], 'status' => 'error'
                ],Response::HTTP_UNPROCESSABLE_ENTITY);
            } else return response([
                'data' => [
                    'message' => [
                        'image' => ['فرمت تصویر نامعتبر'],
                    ]
                ], 'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        } else
            $image = $order->image;

        if (!empty($request->file('gallery'))){
            $gallery = [];
            $file = $request->file('gallery');

            foreach ($file as $item)
            {
                $fileName = $item->getClientOriginalName();
                $fileName = Carbon::now()->timestamp.'-'.$fileName;

                $item->move(storage_path($imagePath),$fileName);
                $files = "storage/orders/{$fileName}";
                $this->imageWatermark($files,'center');
                $gallery[] = $files;
            }
            $galleries = array_merge($old_gallery,$gallery);
        } else {
            $galleries = $old_gallery;
        }

        $data = [
            'slug' => $request['name'],
            'category_id' => $category->id,
            'content' => $request['content'],
            'image' =>  $image,
            'gallery' => implode(',',$galleries),
            'province'  => $request['province'] ?? null,
            'city'  => $request['city'] ?? null,
            'status' => $this->orderRepository::isNewStatus()
        ];
        try {
            DB::beginTransaction();
            $order = $this->orderRepository->update($order,$data);
            $this->orderRepository->syncPlatforms($order,$request['platforms']);
            $this->orderRepository->syncParameters($order,$params);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'data' =>  [
                    'message' => [
                        'order' => ['خظار در ویرایش اگهی']
                    ]
                ], 'status' => 'error'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response([
            'data' => [
                'order' => [
                    'record' => new Order($order)
                ],
                'message' => [
                    'order' => ['اگهی با موفقیت ثبت شد']
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->getUserOrder(Auth::user(),$id);
        if (in_array($order->status,[$this->orderRepository::isNewStatus(),$this->orderRepository::isConfirmedStatus()])){
            $this->orderRepository->delete($order);
            return response([
                'data' => [
                    'message' => [
                        'order' => 'اگهی با موفقیت حذف شد.'
                    ]
                ],'status' => 'success'
            ],Response::HTTP_OK);
        }
        return response([
            'data' => [
                'message' => [
                    'order' => 'برای این اگهی امکان حذف وجود ندارد.'
                ]
            ],'status' => 'error'
        ],Response::HTTP_FORBIDDEN);
    }

    public function deleteImage($order_id , Request $request)
    {
        $order = $this->orderRepository->getUserOrder(Auth::user(),$order_id);
        $request['image'] = str_replace(env('APP_URL').'/', '', $request['image']);
        $validator = Validator::make($request->all(),[
            'image' => 'required|in:'.$order->gallery,
        ],[],[
            'image' => 'تصویر',
        ]);
        if ($validator->fails()){
            return response([
                'data' => [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!in_array($order->status,[$this->orderRepository::isNewStatus(),$this->orderRepository::isConfirmedStatus()])){
            return response([
                'data' => [
                    'message' => [
                        'order' => 'برای این اگهی امکان حذف وجود ندارد.'
                    ]
                ],'status' => 'error'
            ],Response::HTTP_FORBIDDEN);
        }

        $gallery = explode(',',$order->gallery);
        $gallery = \array_diff($gallery, [$request['image']]);
        unlink($request['image']);
        $order->gallery = implode(',',$gallery);
        $this->orderRepository->save($order);
        return response([
            'data' => [
                'message' => [
                    'image' => 'تصویر با موفقیت حذف شد.'
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }
}
