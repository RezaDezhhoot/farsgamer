<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Panel\Card;
use App\Http\Resources\v1\Panel\CardCollection;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Rules\ChekCardNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CardController extends Controller
{
    private $cardRepository , $userRepository;
    public function __construct(CardRepositoryInterface $cardRepository , UserRepositoryInterface $userRepository)
    {
        $this->cardRepository = $cardRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'data' => [
                'cards' => [
                    'records' => new CardCollection($this->userRepository->getUserCards(auth()->user()))
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    public function details()
    {
        return response([
            'data' => [
                'details' => [
                    'banks' => $this->cardRepository->getBank(),
                ],
            ],
            'status' => 'success'
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
        $rateKey = 'card:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 9)) {
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
        $request['card_sheba'] = preg_replace('/\s/','',$request['card_sheba']);
        $request['card_number'] = preg_replace('/\D/','',$request['card_number']);

        $validator = Validator::make($request->all(),[
            'card_number' => ['required',new ChekCardNumber(),'unique:cards'],
            'card_sheba' => ['required','string','regex:/\b^(ir|IR)(\:|\-|\s)?(\d|\s|\-){23,30}\b$/','unique:cards'],
        ],[],[
            'card_number' => 'شماره کارت',
            'card_sheba' => 'شماره شبا',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $bank_code = substr($request['card_number'],0,6);
        $card_data = [
            'user_id' => auth()->id(),
            'card_number' => $request['card_number'],
            'card_sheba' => $request['card_sheba'],
            'bank' => $bank_code,
            'status' => $this->cardRepository::newStatus(),
            'first' => 0
        ];
        $card = $this->cardRepository->create($card_data);
        return response([
            'data' => [
                'card' => new Card($card),
                'message' => [
                    'card' => 'حساب بانکی با موفقیت ایجاد شد.'
                ]
            ],
            'status' => 'success'
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
        $card = $this->userRepository->getUserCard(auth()->user(),$id);
        return response([
            'data' => [
                'card' => [
                    'record' => new Card($card)
                ],
            ],
            'status' => 'success'
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
        $request['card_sheba'] = preg_replace('/\s/','',$request['card_sheba']);
        $request['card_number'] = preg_replace('/\D/','',$request['card_number']);
        $card = $this->userRepository->getUserCard(auth()->user(),$id);
        $validator = Validator::make($request->all(),[
            'card_number' => ['required',new ChekCardNumber(),'unique:cards,card_number,'.($card->id ?? 0)],
            'card_sheba' => ['required','size:26','string','regex:/\b^(ir|IR)(\:|\-|\s)?(\d|\s|\-){23,30}\b$/','unique:cards,card_sheba,'.($card->id ?? 0)],
        ],[],[
            'card_number' => 'شماره کارت',
            'card_sheba' => 'شماره شبا',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $bank_code = substr($request['card_number'],0,6);
        $card_data = [
            'user_id' => auth()->id(),
            'card_number' => $request['card_number'],
            'card_sheba' => $request['card_sheba'],
            'bank' => $bank_code,
            'status' => $this->cardRepository::newStatus(),
        ];
        $card = $this->cardRepository->update($card,$card_data);
        return response([
            'data' => [
                'card' => [
                    'record' => new Card($card)
                ],
                'message' => [
                    'card' => 'حساب بانکی با موفقیت ویرایش شد شد.'
                ]
            ],
            'status' => 'success'
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
        $card = $this->userRepository->getUserCard(auth()->user(),$id);
        $this->cardRepository->delete($card);
        return response([
            'data' => [
                'message' => [
                    'card' => ['حساب بانکی با موفقیت حذف شد.']
                ]
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }
}
