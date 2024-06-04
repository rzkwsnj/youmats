@extends('front.layouts.master')
@section('content')
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="aside_chat">
                    <header>
                        <input type="text" placeholder="search">
                    </header>
                    <ul>
                        @if(count($vendors))
                        @foreach($vendors as $loop_vendor)
                        <li @if($loop_vendor->id == $vendor->id) class="active" @endif>
                            <a href="{{route('chat.user.conversations', [$loop_vendor->id])}}">
                                <div class="online_sdtu user-status-icon user-icon-vendor_{{$loop_vendor->id}}" id="userStatusHead{{$loop_vendor->id}}"></div>
                                <img loading="lazy" width="50px" height="50px" src="{{$loop_vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_50_50')['url']}}" alt="{{$loop_vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['alt']}}" title="{{$loop_vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['title']}}">
                                <div>
                                    <h2>{{$loop_vendor->name}}</h2>
                                    <span class="time_send">
                                        <b>{{date('h:i A', strtotime($loop_vendor->last_message($auth_user->id)->created_at))}}</b>
                                        <small>{{$loop_vendor->count_messages($auth_user->id)}}</small>
                                    </span>
                                    <h3>
                                        <span class="status">
                                            <i class="fa fa-check color_seen" aria-hidden="true"></i>
                                        </span>
                                        {{$loop_vendor->last_message($auth_user->id)->message}}
                                    </h3>
                                </div>
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="main_chat">
                    <header>
                        <div class="online_sdtu_head user-status-icon user-icon-vendor_{{$vendor->id}}" id="userStatusHead{{$vendor->id}}"></div>
                        <img loading="lazy" width="50px" height="50px" src="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_50_50')['url']}}" alt="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['alt']}}" title="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['title']}}">
                        <div>
                            <h2>{{$vendor->name}}</h2>
                            <h3>already {{$vendor->count_messages($auth_user->id)}} messages</h3>
                        </div>
                    </header>
                    <ul id="chat">
                        @if(count($history))
                        @foreach($history as $message)
                            @if($message->sender_type == 'user')
                                <li class="me">
                                    <div class="entete">
                                        <h3 title="{{date('d/m/Y h:i A', strtotime($message->created_at))}}">{{date('h:i A', strtotime($message->created_at))}}</h3>
                                        <h2>{{$auth_user->name}}</h2>
                                        <span class="status status_peaple">
                                            <img loading="lazy" src="{{$auth_user->getFirstMediaUrlOrDefault(USER_PROFILE, 'size_30_30')['url']}}">
                                        </span>
                                    </div>
                                    <div class="triangle"></div>
                                    <div class="message">{{$message->message->message}}</div>
                                </li>
                            @elseif($message->sender_type == 'vendor')
                                <li class="you">
                                    <div class="entete">
                                        <span class="status status_peaple">
                                            <img loading="lazy" src="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_30_30')['url']}}">
                                        </span>
                                        <h2 class="ml-3">{{$vendor->name}}</h2>
                                        <h3 title="{{date('d/m/Y h:i A', strtotime($message->created_at))}}">{{date('h:i A', strtotime($message->created_at))}}</h3>
                                    </div>
                                    <div class="triangle"></div>
                                    <div class="message">{{$message->message->message}}</div>
                                </li>
                            @endif
                        @endforeach
                        @endif
                    </ul>
                    <footer>
                        <textarea placeholder="Type your message" class="chat-input"></textarea>
{{--                        <a href="#">Send</a>--}}
                    </footer>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('chat')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(function () {
                let chatInput = $(".chat-input");
                let chatInputToolbar = $(".chat-input-toolbar");
                let chatBody = $(".chat-body");
                let chatContainer = $("#chat");

                let user_id = "user_{{$auth_user->id}}";
                let ip_address = '{{env('SOCKET_HOST')}}';
                let socket_port = '8005';
                let socket = io(ip_address + ':' + socket_port);
                let receiver_id = "{{ $vendor->id }}";

                socket.on('connect', function () {
                    socket.emit('user_connected', user_id);
                });

                socket.on('updateUserStatus', (data) => {
                    let userStatusIcon = $('.user-status-icon');
                    userStatusIcon.addClass('d-none');
                    $.each(data, function (key, val) {
                        if (val !== null && val !== 0) {
                            let userIcon = $(".user-icon-" + key);
                            userIcon.removeClass('d-none');
                        }
                    })
                });

                chatInput.keypress(function (e) {
                    let message = $(this).val();
                    if (e.which === 13 && !e.shiftKey) {
                        chatInput.val('');
                        sendMessage(message);
                        return false;
                    }
                });

                function sendMessage(message) {
                    let url = "{{ route('chat.send_message') }}";
                    let form = $(this);
                    let formData = new FormData();
                    let token = "{{csrf_token()}}";

                    formData.append('message', message);
                    formData.append('_token', token);
                    formData.append('receiver_id', receiver_id);
                    formData.append('sender_type', 'user');
                    formData.append('receiver_type', 'vendor');
                    formData.append('guardName', 'web');

                    appendMessageToSender(message);

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'JSON',
                        success: function (response) {
                            if (response.success) {
                                console.log(response.data);
                            }
                        }
                    });
                }

                function appendMessageToSender(message) {
                    let name = '{{ $auth_user->name }}';
                    let image = '{!! $auth_user->getFirstMediaUrlOrDefault(USER_PROFILE, 'size_30_30')['url'] !!}';
                    let newMessage =
                        `<li class="me">
                            <div class="entete">
                                <h3 title="` + getCurrentDateTime() + `">` + getCurrentTime() + `</h3>
                                <h2>` + name + `</h2>
                                <span class="status status_peaple">
                                    <img loading="lazy" src="` + image + `">
                                </span>
                            </div>
                            <div class="triangle"></div>
                            <div class="message">` + message + `</div>
                        </li>`;
                    chatContainer.append(newMessage);
                }

                function appendMessageToReceiver(message) {
                    let name = '{{ $vendor->name }}';
                    let image = '{!! $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_30_30')['url'] !!}';
                    let newMessage =
                        `<li class="you">
                        <div class="entete">
                            <span class="status status_peaple">
                                <img loading="lazy" width="30px" height="30px" src="` + image + `">
                            </span>
                            <h2 class="ml-3">` + name + `</h2>
                            <h3 title="` + dateFormat(message.created_at) + `">` + timeFormat(message.created_at) + `</h3>
                        </div>
                        <div class="triangle"></div>
                        <div class="message">` + message.content + `</div>
                    </li>`;
                    chatContainer.append(newMessage);
                }

                socket.on("private-channel:App\\Events\\PrivateMessageEvent", function (message) {
                    appendMessageToReceiver(message);
                });

                function updateScroll() {
                    var element = document.getElementById("chat");
                    element.scrollTop = element.scrollHeight;
                }

                updateScroll();

            });
        });
    </script>
@endpush
