@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left">
        <div class="row">
            <div class="col-xl-8 col-xxl-9">
                <div class="card border-0 mb-0 rounded-lg overflow-hidden">
                    <div class="player shadow-none">
                        <div style="padding:56.25% 0 0 0;position:relative;">{!!$language? json_decode($lesson->video)->ru : json_decode($lesson->video)->uz!!}</div><script src="https://player.vimeo.com/api/player.js"></script>
                        
                    </div>
                </div>
                <div class="card d-block border-0 rounded-lg overflow-hidden dark-bg-transparent bg-transparent mt-4 pb-3">
                    <div class="row">
                        <div class="col-10"><h2 class="fw-700 font-md d-block lh-4 mb-2">{{$language? json_decode($lesson->lessonTitleName)->ru : json_decode($lesson->lessonTitleName)->uz}}</h2></div>
                        <div class="col-2">
                            
                            <div class="dropdown-menu dropdown-menu-right p-3 border-0 shadow-xss" aria-labelledby="dropdownMenu2">
                                <ul class="d-flex align-items-center mt-0 float-left">
                                    <li class="mr-2"><h4 class="fw-600 font-xss text-grey-900  mt-2 mr-3">Share: </h4></li>
                                    <li class="mr-2"><a href="#" class="btn-round-md bg-facebook"><i class="font-xs ti-facebook text-white"></i></a></li>
                                    <li class="mr-2"><a href="#" class="btn-round-md bg-twiiter"><i class="font-xs ti-twitter-alt text-white"></i></a></li>
                                    <li class="mr-2"><a href="#" class="btn-round-md bg-linkedin"><i class="font-xs ti-linkedin text-white"></i></a></li>
                                    <li class="mr-2"><a href="#" class="btn-round-md bg-instagram"><i class="font-xs ti-instagram text-white"></i></a></li>
                                    <li class="mr-2"><a href="#" class="btn-round-md bg-pinterest"><i class="font-xs ti-pinterest text-white"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <span class="font-xssss fw-700 text-grey-900 d-inline-block ml-0 text-dark">{{$language? (isset(json_decode($lesson->course->getinfo->courseTitleName)->ru)? json_decode($lesson->course->getinfo->courseTitleName)->ru : '') : (isset(json_decode($lesson->course->getinfo->courseTitleName)->uz)? json_decode($lesson->course->getinfo->courseTitleName)->uz : '')}}</span>
                   
                    
                </div>
                <style>
                    .btn-custom{
                        background-color: #4DB1B1;
                        color: white;
                    }
                    .btn-custom:hover{
                        opacity: 0.9;
                        color: white;
                    }
                    .des span{
                        margin: 14px;
                        display: block;
                    }
                    .tag{
                        background-color: #4DB1B1;
                        padding: 10px;
                        color: white;
                        display:block;
                    }
                    @media only screen and (max-width: 600px) {
                         .mod{
                        display: flex!important;
                        flex-direction: revert;
                        padding: 0!important;
                        gap: 8px;
                    }
                    }
                   
                </style>
               
                <div class="card d-block border-0 text-right rounded-lg overflow-hidden p-4 shadow-xss mt-4 mod">
                   
                    @if($lesson->contents->count()>0)
                    <button type="button" onclick="document.querySelector('#showingid').style.display='block'; window.location.href='#showingid'" class="btn btn-custom mt-2 mt-md-0"><svg class="mr-1" width="19" height="16" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.25 0.773438H14.8875C13.7367 0.773438 12.6117 1.10391 11.6437 1.72734L10.5 2.46094L9.35625 1.72734C8.38924 1.10403 7.26299 0.772823 6.1125 0.773438H0.75C0.335156 0.773438 0 1.10859 0 1.52344V14.8359C0 15.2508 0.335156 15.5859 0.75 15.5859H6.1125C7.26328 15.5859 8.38828 15.9164 9.35625 16.5398L10.3969 17.2102C10.4273 17.2289 10.4625 17.2406 10.4977 17.2406C10.5328 17.2406 10.568 17.2313 10.5984 17.2102L11.6391 16.5398C12.6094 15.9164 13.7367 15.5859 14.8875 15.5859H20.25C20.6648 15.5859 21 15.2508 21 14.8359V1.52344C21 1.10859 20.6648 0.773438 20.25 0.773438ZM6.1125 13.8984H1.6875V2.46094H6.1125C6.94219 2.46094 7.74844 2.69766 8.44453 3.14531L9.58828 3.87891L9.75 3.98438V14.8125C8.63437 14.2125 7.3875 13.8984 6.1125 13.8984ZM19.3125 13.8984H14.8875C13.6125 13.8984 12.3656 14.2125 11.25 14.8125V3.98438L11.4117 3.87891L12.5555 3.14531C13.2516 2.69766 14.0578 2.46094 14.8875 2.46094H19.3125V13.8984ZM7.80234 5.46094H3.44766C3.35625 5.46094 3.28125 5.54063 3.28125 5.63672V6.69141C3.28125 6.7875 3.35625 6.86719 3.44766 6.86719H7.8C7.89141 6.86719 7.96641 6.7875 7.96641 6.69141V5.63672C7.96875 5.54063 7.89375 5.46094 7.80234 5.46094ZM13.0312 5.63672V6.69141C13.0312 6.7875 13.1062 6.86719 13.1977 6.86719H17.55C17.6414 6.86719 17.7164 6.7875 17.7164 6.69141V5.63672C17.7164 5.54063 17.6414 5.46094 17.55 5.46094H13.1977C13.1062 5.46094 13.0312 5.54063 13.0312 5.63672ZM7.80234 8.74219H3.44766C3.35625 8.74219 3.28125 8.82188 3.28125 8.91797V9.97266C3.28125 10.0688 3.35625 10.1484 3.44766 10.1484H7.8C7.89141 10.1484 7.96641 10.0688 7.96641 9.97266V8.91797C7.96875 8.82188 7.89375 8.74219 7.80234 8.74219ZM17.5523 8.74219H13.1977C13.1062 8.74219 13.0312 8.82188 13.0312 8.91797V9.97266C13.0312 10.0688 13.1062 10.1484 13.1977 10.1484H17.55C17.6414 10.1484 17.7164 10.0688 17.7164 9.97266V8.91797C17.7188 8.82188 17.6438 8.74219 17.5523 8.74219Z" fill="#E6F0F0"/>
                        </svg>{{ __('message.readlesson')}}</button>
                        @endif
                        @if($lesson->quizes)
                        <div href="" onclick="this.preventDefault();"  id="myBtn" class="btn btn-primary mt-2 mt-md-0">{{ __('message.startquiz')}} <svg class="ml-1" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.25 22.5C16.2117 22.5 15.1966 22.1921 14.3333 21.6152C13.4699 21.0383 12.797 20.2184 12.3996 19.2591C12.0023 18.2998 11.8983 17.2442 12.1009 16.2258C12.3035 15.2074 12.8035 14.2719 13.5377 13.5377C14.2719 12.8035 15.2074 12.3035 16.2258 12.1009C17.2442 11.8983 18.2998 12.0023 19.2591 12.3996C20.2184 12.797 21.0383 13.4699 21.6152 14.3333C22.1921 15.1966 22.5 16.2117 22.5 17.25C22.4984 18.6419 21.9448 19.9763 20.9606 20.9606C19.9763 21.9448 18.6419 22.4984 17.25 22.5ZM17.25 13.5C16.5083 13.5 15.7833 13.7199 15.1666 14.132C14.5499 14.544 14.0693 15.1297 13.7855 15.8149C13.5016 16.5002 13.4274 17.2542 13.5721 17.9816C13.7168 18.709 14.0739 19.3772 14.5984 19.9017C15.1228 20.4261 15.791 20.7833 16.5184 20.9279C17.2458 21.0726 17.9998 20.9984 18.6851 20.7146C19.3703 20.4307 19.956 19.9501 20.368 19.3334C20.7801 18.7167 21 17.9917 21 17.25C20.999 16.2557 20.6036 15.3025 19.9006 14.5994C19.1975 13.8964 18.2443 13.501 17.25 13.5Z" fill="white"/>
                            <path d="M19.5 18.4395L18 16.9395V15H16.5V17.5605L18.4395 19.5L19.5 18.4395ZM6 12H10.5V13.5H6V12ZM6 7.5H15V9H6V7.5Z" fill="white"/>
                            <path d="M19.5 3C19.4996 2.6023 19.3414 2.221 19.0602 1.93978C18.779 1.65856 18.3977 1.5004 18 1.5H3.00001C2.6023 1.5004 2.221 1.65856 1.93978 1.93978C1.65856 2.221 1.5004 2.6023 1.50001 3V12.75C1.49833 14.2458 1.90404 15.7137 2.67359 16.9964C3.44313 18.279 4.54745 19.3278 5.868 20.0303L9.75 22.1003V20.4L6.5745 18.7065C5.49402 18.1318 4.59042 17.2737 3.96069 16.2243C3.33095 15.1749 2.99884 13.9738 3.00001 12.75V3H18V9.75H19.5V3Z" fill="white"/>
                            </svg> </div>
                            @endif
                </div>
                <style>
                   
                    
                    /* The Modal (background) */
                    .modal {
                      display: none; /* Hidden by default */
                      position: fixed; /* Stay in place */
                      z-index: 1; /* Sit on top */
                      padding-top: 100px; /* Location of the box */
                      left: 0;
                      top: 0;
                      width: 100%; /* Full width */
                      height: 100%; /* Full height */
                      overflow: auto; /* Enable scroll if needed */
                      background-color: rgb(0,0,0); /* Fallback color */
                      background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
                    }
                    
                    /* Modal Content */
                    .modal-content {
                      background-color: #fefefe;
                      margin: auto;
                      padding: 20px;
                      border: 1px solid #888;
                      max-width: 380px;
                    }
                    
                    /* The Close Button */
                    .close {
                      color: #aaaaaa;
                      float: right;
                      font-size: 28px;
                      font-weight: bold;
                    }
                    
                    .close:hover,
                    .close:focus {
                      color: #000;
                      text-decoration: none;
                      cursor: pointer;
                    }
                    </style>
                           <div id="myModal" class="modal">

                              <!-- Modal content -->
                              <div class="modal-content">
                                <span class="close">&times;</span>
                                <p>{{ __('message.startquizrightnow')}}</p>
                                <a class="btn btn-primary" href="{{route('lesson.quiz.web',['id'=>$lesson->id])}}">{{$language? 'Да' : 'Ha' }} </a>
                              </div>
                            
                            </div>
                            <script>
                                // Get the modal
                            var modal = document.getElementById("myModal");
                            
                            // Get the button that opens the modal
                            var btn = document.getElementById("myBtn");
                            
                            // Get the <span> element that closes the modal
                            var span = document.getElementsByClassName("close")[0];
                            
                            // When the user clicks the button, open the modal 
                            btn.onclick = function() {
                              modal.style.display = "block";
                            }
                            
                            // When the user clicks on <span> (x), close the modal
                            span.onclick = function() {
                              modal.style.display = "none";
                            }
                            
                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function(event) {
                              if (event.target == modal) {
                                modal.style.display = "none";
                              }
                            }
                            </script>
                <div id="showingid" class="card d-block border-0 rounded-lg overflow-hidden p-4 shadow-xss mt-4" style="display:none!important" >
                    
                    <p class="des font-xssss fw-500 lh-28 text-grey-600 mb-0 pl-2">
                       
                        @forelse ($lesson->contents as $content)
                          
                            @if($content->type=='img')
                            <img style="width: 100%" src="http://api.895773-cx81958.tmweb.ru/files/lessons/{{$language? json_decode($content->body)->ru : json_decode($content->body)->uz}}" alt="icon" class="p-2">
                            @elseif($content->type=='Таг' || $content->type=='tag')
                           <span class="tag"> {!! $language?  (isset(json_decode($content->body)->ru)?  json_decode($content->body)->ru : '')
                           :  (isset(json_decode($content->body)->uz)?  json_decode($content->body)->uz : '') !!}</span>
                            @else
                            <span class="text"> {!! $language?  (isset(json_decode($content->body)->ru)?  json_decode($content->body)->ru : '')
                           :  (isset(json_decode($content->body)->uz)?  json_decode($content->body)->uz : '') !!}</span>
                            @endif
                        
                        @empty
                            
                        @endforelse
                        
                        
                                                            </p>
                </div>

            </div>
            <div class="col-xl-4 col-xxl-3 pb-4">
               
                

                <div class="card shadow-xss rounded-lg border-0 p-4 mb-4">
                    <h4 class="font-xs fw-700 text-grey-900 d-block mb-3">{{ __('message.listlessons')}} <a href="#" class="float-right"><i class="ti-arrow-circle-right text-grey-500 font-xss"></i></a></h4>
                    @forelse ($alllessons as $k=>$lesson)
                    <a href="{{route('lesson.web',['id'=>$lesson->id])}}" class="card-body d-flex p-0">
                        <span class="bg-current btn-round-xs rounded-lg font-xssss text-white fw-600">{{$k+1}}</span>
                        <span class="font-xssss fw-500 text-grey-500 ml-2">{{$language? json_decode($lesson->lessonTitleName)->ru : json_decode($lesson->lessonTitleName)->uz}}</span>
                        <span class="ml-auto font-xssss fw-500 text-grey-500">{{$language? gmdate("i:s", json_decode($lesson->videoLength)->ru) : gmdate("i:s", json_decode($lesson->videoLength)->uz)}}</span>
                    </a>
                    @empty
                        
                    @endforelse
                    
                </div>
            </div>

        </div>
    </div>
    @include('layouts.right')
</div>  
@endsection