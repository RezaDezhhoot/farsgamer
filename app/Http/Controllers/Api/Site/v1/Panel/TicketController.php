<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Panel\Ticket;
use App\Http\Resources\v1\Panel\TicketCollection;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    public $ticketRepository , $userRepository , $settingRepository;
    public function __construct(
        TicketRepositoryInterface $ticketRepository ,
        UserRepositoryInterface $userRepository ,
        SettingRepositoryInterface $settingRepository
    )
    {
        $this->ticketRepository = $ticketRepository;
        $this->userRepository = $userRepository;
        $this->settingRepository = $settingRepository;
    }

    public function details()
    {
        return response([
            'data' => [
                'details' => [
                    'subjects' => $this->settingRepository->getSubjects('subject'),
                    'priorities' => $this->ticketRepository::getPriority(),
                    'max_files_count' => 4,
                    'min_files_count' => 0,
                    'files_size_unt' => 'KB',
                    'max_files_size' => 2048,
                ],
            ],
            'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = $this->ticketRepository->getUserTickets(Auth::user());
        return response([
            'data' => [
                'tickets' => [
                    'records' => new TicketCollection($tickets),
                    'paginate' => [
                        'total' => $tickets->total(),
                        'count' => $tickets->count(),
                        'per_page' => $tickets->perPage(),
                        'current_page' => $tickets->currentPage(),
                        'total_pages' => $tickets->lastPage()
                    ]
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
        $rateKey = 'verify-attempt:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 35)) {
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
        RateLimiter::hit($rateKey, 1 * 60 * 60);
        $validator = Validator::make($request->all(),[
            'subject' => ['required','string','in:'.implode(',',$this->settingRepository->getSubjects('subject'))],
            'content' => ['required','string','max:18500'],
            'file' => ['array','min:0','max:4'],
            'file.*' => ['nullable','mimes:'.$this->settingRepository->getSiteFaq('valid_ticket_files'),'max:2048'],
            'priority' => ['required','in:'.implode(',',array_keys($this->ticketRepository::getPriority()))],
        ],[],[
            'subject' => 'موضوع',
            'content' => 'متن درخواست',
            'file' => 'فایل',
            'priority' => 'الویت',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $rateKey2 = 'ticket:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey2, $this->settingRepository->getSiteFaq('ticket_per_day'))) {
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
        RateLimiter::hit($rateKey2, 24 * 60 * 60);
        $files = [];
        if (!empty($request->file('file'))){
            $file = $request->file('file');

            $imagePath = "app/public/tickets";
            foreach ($file as $item)
            {
                $fileName = $item->getClientOriginalName();
                $fileName = Carbon::now()->timestamp.'-'.$fileName;

                $item->move(storage_path($imagePath),$fileName);
                $files[] = "storage/tickets/{$fileName}";
            }

        }
        $ticket = [
            'subject' => $request['subject'],
            'content' => $request['content'],
            'sender_id' => Auth::id(),
            'sender_type' => $this->ticketRepository::user(),
            'priority' => $request['priority'],
            'status' => $this->ticketRepository::pendingStatus(),
            'file' => implode(',',$files),
        ];
        $ticket = $this->ticketRepository->create(Auth::user(),$ticket);
        return response([
            'data' => [
                'ticket' => [
                    'record' => new Ticket($ticket)
                ],
                'message' => [
                    'ticket' => ['تیکت یا موفقیت ارسال شد'],
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function show($id)
    {
        return [
            'data' => [
                'ticket' => [
                    'record' => new Ticket($this->ticketRepository->userTicketFind(Auth::user(),$id))
                ],
            ], 'status' => Response::HTTP_OK
        ];
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
        $ticket = $this->ticketRepository->userTicketFind(Auth::user(),$id);
        $rateKey = 'verify-attempt:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 35)) {
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
        RateLimiter::hit($rateKey, 1 * 60 * 60);
        $validator = Validator::make($request->all(),[
            'content' => ['required','string','max:18500'],
            'file' => ['array','min:0','max:4'],
            'file.*' => ['nullable','mimes:'.$this->settingRepository->getSiteFaq('valid_ticket_files'),'max:2048'],
        ],[],[
            'content' => 'متن درخواست',
            'file' => 'فایل',
        ]);
        if ($validator->fails()){
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $rateKey2 = 'ticket:' . auth('api')->id() . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey2, $this->settingRepository->getSiteFaq('ticket_per_day'))) {
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
        RateLimiter::hit($rateKey2, 24 * 60 * 60);
        $files = [];
        if (!empty($request->file('file'))){
            $file = $request->file('file');

            $imagePath = "app/public/tickets";
            foreach ($file as $item)
            {
                $fileName = $item->getClientOriginalName();
                $fileName = Carbon::now()->timestamp.'-'.$fileName;

                $item->move(storage_path($imagePath),$fileName);
                $files[] = "storage/tickets/{$fileName}";
            }
        }
        $new_ticket = [
            'subject' => $ticket->subject,
            'content' => $request['content'],
            'sender_id' => Auth::id(),
            'parent_id' => $ticket->id,
            'sender_type' => $this->ticketRepository::user(),
            'priority' => $ticket->priority,
            'status' => $this->ticketRepository::userAnsweredStatus(),
            'file' => implode(',',$files),
        ];
        $this->ticketRepository->create(Auth::user(),$new_ticket);
        $ticket->status = $this->ticketRepository::userAnsweredStatus();
        $this->ticketRepository->save($ticket);
        return response([
            'data' => [
                'ticket' => [
                    'record' => new Ticket($ticket)
                ],
                'message' => [
                    'ticket' => ['تیکت یا موفقیت ارسال شد'],
                ]
            ],'status' => 'success'
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        $this->ticketRepository->delete($this->ticketRepository->userTicketFind(Auth::user(),$id));
        return [
            'data' => [
                'message' => [
                    'ticket' => ['تیکت با موفقیت حذف شد.']
                ]
            ], 'status' => Response::HTTP_OK
        ];
    }
}
