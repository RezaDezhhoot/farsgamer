<div>
    <x-admin.form-control store="{{false}}" title="چت های من" />
    <div class="d-flex flex-row">
        <div class="flex-row-auto offcanvas-mobile w-350px w-xl-400px" id="kt_chat_aside" wire:ignore.self>
            <!--begin::Card-->
            <div class="card card-custom" wire:ignore.self>
                <!--begin::Body-->
                <div class="card-body" wire:ignore.self>
                    <!--begin:Search-->
                    <div class="input-group input-group-solid" wire:ignore.self>
                        <div class="input-group-prepend" wire:ignore.self>
                        </div>
                    </div>
                    <!--end:Search-->
                    <!--begin:Users-->
                    <div wire:ignore.self class="mt-7 scroll scroll-pull">
                    @foreach($groups as $group)
                        <!--begin:User-->
                            <div style="cursor: pointer;border: 1px solid aliceblue;border-radius: 5px;padding: 15px;" class="d-flex align-items-center justify-content-between mb-5" wire:click="openChatList('{{$group->id}}')">
                                <div class="d-flex align-items-center">
                                    <div class="symbol-group symbol-hover justify-content-center">
                                        @if($group->user1 != auth()->id())
                                            <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ $group->user_one->user_name }}">
                                                <img alt="Pic" src="{{ asset($group->user_one->profile_image) }}" />
                                            </div>
                                        @else
                                            <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ $group->user_two->user_name }}">
                                                <img alt="Pic" src="{{ asset($group->user_two->profile_image) }}" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        @if($group->user1 != auth()->id())
                                            <a class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">
                                                {{ $group->user_one->user_name }},
                                            </a>
                                            <span class="text-muted font-weight-bold font-size-sm">
                                               وضیعت :{{ $group->statusLabel }}
                                            </span>
                                        @elseif($group->user1 == auth()->id() && $group->user2 == auth()->id())
                                            <a class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">
                                                Saved Messages
                                            </a>
                                        @else
                                            <a class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">
                                                {{ $group->user_two->user_name }}
                                            </a>
                                            <span class="text-muted font-weight-bold font-size-sm">
                                               وضیعت :{{ $group->statusLabel }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                        <span class="text-muted font-weight-bold font-size-sm">
                                            {{ $group->last }}
                                        </span>
                                </div>
                            </div>
                            <!--end:User-->
                        @endforeach
                    </div>
                    <!--end:Users-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <div class="flex-row-fluid ml-lg-8" id="kt_chat_content" wire:ignore.self>
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Header-->
                <div class="card-header align-items-center px-4 py-3" wire:ignore.self>
                    <div class="text-left flex-grow-1">
                        <div class="symbol-group symbol-hover">
                            @if(!is_null($chatList) && !empty($chatList))
                                <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ $chatList->user_one->user_name }}">
                                    <img alt="Pic" src="{{ asset($chatList->user_one->profile_image) }}" />
                                </div>
                                <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="{{ $chatList->user_two->user_name }}">
                                    <img alt="Pic" src="{{ asset($chatList->user_two->profile_image) }}" />
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-center flex-grow-1">
                        @if(!is_null($chatList) && !empty($chatList))
                            @if($chatList->user1 != auth()->id())
                                <div class="text-dark-75 font-weight-bold font-size-h5">
                                    {{ $chatList->user_one->user_name }}
                                </div>
                            @elseif($chatList->user1 == auth()->id() && $chatList->user2 == auth()->id())
                                <div class="text-dark-75 font-weight-bold font-size-h5">Saved Messages</div>
                            @else
                                <div class="text-dark-75 font-weight-bold font-size-h5">
                                    {{ $chatList->user_two->user_name }}
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="text-right flex-grow-1">
                        <div class="symbol symbol-35 symbol-circle" data-toggle="tooltip">
                            @if(!is_null($chatList) && !empty($chatList))
                                وضیعت :  {{ $chatList->statusLabel }}
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body" wire:ignore.self>
                    <!--begin::Scroll-->
                    <div class="scroll scroll-pull" data-mobile-height="350" wire:ignore.self>
                        <!--begin::Messages-->
                        <div class="messages">
                            @if(!is_null($chatList) && !empty($chatList))
                                @foreach($chatList->chats as $item)
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
                    <textarea wire:model.defer="chatText" wire:keydown.enter="sendChatText" class="form-control border-0 p-0" rows="2" placeholder="پیامی بنویسید"></textarea>
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
