@props(['chats','id'])
<div>
    <div class="modal modal-sticky modal-sticky-bottom-right" id="{{$id}}" role="dialog" data-backdrop="false" wire:ignore.self>
        <div class="modal-dialog" role="document" wire:ignore.self>
            <div wire:ignore.self class="modal-content">
                <!--begin::Card-->
                <div wire:ignore.self class="card card-custom">
                    <!--begin::Header-->
                    <div wire:ignore.self class="card-header align-items-center px-4 py-3">
                        <div class="text-left flex-grow-1">
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="text-dark-75 font-weight-bold font-size-h5">
                                @if(!is_null($chats) &&  $chats->user1 == auth()->id() &&  $chats->user2 == auth()->id())
                                    Saved Messages
                                @else
                                    {{ !is_null($chats) ? $chats->user1 == auth()->id() ? $chats->user_two->user_name : $chats->user_one->user_name : '' }}
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-grow-1">
                            <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-dismiss="modal">
                                <i class="ki ki-close icon-1x"></i>
                            </button>
                        </div>
                    </div>
                    <div wire:ignore.self class="card-body">
                        <!--begin::Scroll-->
                        <div wire:ignore.self id="scroll-pull" class="scroll scroll-pull" data-height="375" data-mobile-height="300">
                            <!--begin::Messages-->
                            <div class="messages">
                                @if(!is_null($chats))
                                    @foreach($chats->chats as $item)
                                        @if($item->sender_id == auth()->id())
                                            <div class="d-flex flex-column mb-5 align-items-start">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-circle symbol-35 mr-3">
                                                        <img alt="Pic" src="{{ asset(auth()->user()->profile_image) }}" />
                                                    </div>
                                                    <div>
                                                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">شما</a>
                                                        <span class="text-muted font-size-sm">
                                                     {{ $item->created_at->diffForHumans() }}
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                                                    {{ $item->content }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex flex-column mb-5 align-items-end">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <span class="text-muted font-size-sm">{{ $item->created_at->diffForHumans() }}</span>
                                                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">
                                                            {{ $item->sender->user_name }}
                                                        </a>
                                                    </div>
                                                    <div class="symbol symbol-circle symbol-35 ml-3">
                                                        <img alt="Pic" src="{{ $item->sender->profile_image }}" />
                                                    </div>
                                                </div>
                                                <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
                                                    {{ $item->content }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <!--end::Messages-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div wire:ignore.self class="card-footer align-items-center">
                        <!--begin::Compose-->
                        <textarea wire:model.defer="chatText" class="form-control border-0 p-0" wire:keydown.enter="sendChatText" rows="2" placeholder="پیامی بنویسید"></textarea>
                        <div class="d-flex align-items-center justify-content-between mt-5">
                            <div class="mr-3">
                            </div>
                            <div>
                                <button wire:click="sendChatText()" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6">ارسال</button>
                            </div>
                        </div>
                        <!--begin::Compose-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Card-->
            </div>
        </div>
    </div>
</div>

