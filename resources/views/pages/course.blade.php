@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left">
        <div class="row">
            <style>
                .newstyle{
                    background-repeat: no-repeat;
                    background-size: 100%;
                    background-position: center center;
                    height: 350px;
                }
                .bd p{
                    font-size:14px!important;
                }
                .savedcourse{
                  background-color: transparent;
                  border: 1px solid #E50202;
                  cursor: pointer;
                }
                .savedcourse  .feather-bookmark::before{
                  color: #E50202;
                }
                .savedcourse.active, .savedcourse:hover{
                  background-color: #E50202;
                }
                .savedcourse.active  .feather-bookmark::before, .savedcourse:hover  .feather-bookmark::before{
                  color: white;
                }
            </style>
            <div class="col-xl-8 col-xxl-9">
                <div class="card border-0 mb-0 rounded-lg overflow-hidden newstyle" style="background-image:
                  @if($language)
                        @if(isset(json_decode($course->getinfo->courseBanner)->uz))
                        url(http://api.895773-cx81958.tmweb.ru/files/course/{{json_decode($course->getinfo->courseBanner)->ru}})
                        @endif
                    @else
                        @if(isset(json_decode($course->getinfo->courseBanner)->uz))
                        url(http://api.895773-cx81958.tmweb.ru/files/course/{{json_decode($course->getinfo->courseBanner)->uz}})
                        @endif
                    @endif
                
                 
                 
                 ;">
                    <div class="card-body p-5 bg-black-08">
                        <span class="font-xsssss fw-700 pl-3 pr-3 lh-32 text-uppercase rounded-lg ls-2 alert-warning d-inline-block text-warning mr-1">{{$language? (isset(json_decode($course->category->categoryName)->ru)? json_decode($course->category->categoryName)->ru : '') : (isset(json_decode($course->category->categoryName)->uz)? json_decode($course->category->categoryName)->uz : '')}}</span>
                        <h2 class="fw-700 font-lg d-block lh-4 mb-1 text-white mt-2">{{$language? (isset(json_decode($course->getinfo->courseTitleName)->ru)? json_decode($course->getinfo->courseTitleName)->ru : '') : (isset(json_decode($course->getinfo->courseTitleName)->uz)? json_decode($course->getinfo->courseTitleName)->uz : '')}}</h2>
                        

                        <div class="clearfix"></div>
                     
                       

                       
                    </div>
                </div>


                

              

                <div class="card d-block border-0 rounded-lg overflow-hidden p-4 shadow-xss mt-4 bd">
                    
                    <h2 class="fw-700 font-sm mb-3 mt-1 pl-1 mb-3">{{ __('message.information')}}  <a @if(!$wishlish) onclick="savedCourse(this,{{$course->id}})" @else  onclick="savedCourseRemove(this,{{$wishlish->id}})" @endif class="savedcourse btn-round-md ml-3 d-inline-block float-right rounded-lg {{$wishlish? 'active': ''}}"><i class="feather-bookmark font-sm text-white"></i></a> </h2>
                    <p class="font-xssss fw-500 lh-28 text-grey-600 mb-0 pl-2">{!!$language? (isset(json_decode($course->getinfo->courseInfo)->ru)? json_decode($course->getinfo->courseInfo)->ru : '') : (isset(json_decode($course->getinfo->courseInfo)->uz)? json_decode($course->getinfo->courseInfo)->uz : '')!!} </p>
                   
                  </div>

                
            </div>
            <div class="col-xl-4 col-xxl-3 mb-5">
                <div class="card p-4 mb-4 bg-primary border-0 shadow-xss rounded-lg" style="background-color: #017383!important" >
                                <div class="card-body">
                                 <h2 class="text-white font-xsssss fw-700 text-uppercase ls-3 "> @if(!$course->coursePrice) {{ __('message.winn')}}  @else  {{ __('message.notfree')}}  @endif</h2>  
                                    <h1 class="display2-size text-white fw-700"><svg width="58" height="58" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="24" fill="#FFF176"/>
                            <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"/>
                        </svg> @if($course->coursePrice) <span style="color:#f2bc23;">{{$course->coursePrice}}</span> @else  <span style="color:#03cdbe">{{ isset($course->lessons[0])? $course->lessons[0]->quizes?->prizeIQC : 0 }} </span> @endif</h1>
                                    <h4 class="text-white fw-500 mb-4 lh-24 font-xssss">@if($course->coursePrice) {{ __('message.complex')}} @else {{ __('message.getiqc')}}  @endif.</h4>
                                    
                                  @if($course->coursePrice>($iqc? $iqc->amountofIQC : 0 ))   <a id="myBtn" style="color:#027284 !important" class="btn btn-block border-0 w-100 bg-white p-3 text-primary fw-600 rounded-lg d-inline-block font-xssss btn-light">  {{ __('message.purchase')}}   </a>
                                  
                                  @else
                                  <a href="{{route('lesson.web',['id'=>$course->lessons[0]?->id])}}" style="color:#027284 !important"  class="btn btn-block border-0 w-100 bg-white p-3 text-primary fw-600 rounded-lg d-inline-block font-xssss btn-light">  {{ __('message.begin')}} </a>
                                  @endif

                                </div>
                            </div>

                <div class="card shadow-xss rounded-lg border-0 p-4 mb-4">
                    <h4 class="font-xs fw-700 text-grey-900 d-block mb-3">{{ __('message.lessonList')}} <a href="#" class="float-right"><i class="ti-arrow-circle-right text-grey-500 font-xss"></i></a></h4>
                    @forelse ($course->lessons as $k=>$lesson)
                    <a href="{{route('lesson.web',['id'=>$lesson->id])}}" class="card-body d-flex p-0">
                        <span class="bg-current btn-round-xs rounded-lg font-xssss text-white fw-600">{{$k+1}}</span>
                        <span class="font-xssss fw-500 text-grey-500 ml-2">{{$language? (isset(json_decode($lesson->lessonTitleName)->ru)? json_decode($lesson->lessonTitleName)->ru : '') : (isset(json_decode($lesson->lessonTitleName)->uz)? json_decode($lesson->lessonTitleName)->uz : '')}}</span>
                        <span class="ml-auto font-xssss fw-500 text-grey-500">{{$language? gmdate("i:s", (isset(json_decode($lesson->videoLength)->ru)? json_decode($lesson->videoLength)->ru : '')) : gmdate("i:s", (isset(json_decode($lesson->videoLength)->uz)? json_decode($lesson->videoLength)->uz : ''))}}</span>
                    </a>
                    @empty
                        
                    @endforelse
                    
                </div>
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
                      text-align:center;
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
                                <p>{{$language? 'У Вас не достаточно IQC' : 'SIzda IQC yetarlik emas' }}</p>
                               
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
                           
                            function savedCourse(e,course_id){
                              const data = { 
                                course_id: course_id,
                                platform: 'website',
                                device: 'desktop',
                                timeZone: '500',
                                browser: 'chrome',

                               };
                              fetch("{{route('savedcoursePost.web')}}",{
                                method: 'POST',
                                body: JSON.stringify(data),
                                headers: {
                                  'Content-Type': 'application/json'
                                }

                              })
                                        .then((resp) => resp.json()).then(function(data){
                                                if(data.wishlist){
                                                  e.classList.add('active');
                                                  e.setAttribute('onclick','savedCourseRemove(this,'+data.wishlist.id+')');
                                                }
                                            })
                                                .catch(function(error) {
                                                    console.log(error);
                                                });
                            }
                            function savedCourseRemove(e, wish_id){
                              const data = { 
                                wish_id: wish_id,
                                status: 'removed',
                                platform: 'website',
                                device: 'desktop',
                                timeZone: '500',
                                browser: 'chrome',

                               };
                              fetch("{{route('savedcourseRemove.web')}}",{
                                method: 'POST',
                                body: JSON.stringify(data),
                                headers: {
                                  'Content-Type': 'application/json'
                                }

                              })
                                        .then((resp) => resp.json()).then(function(data){
                                                if(data.message =='removed'){
                                                  e.classList.remove('active');
                                                  e.setAttribute('onclick','savedCourse(this,{{$course->id}})');
                                                }
                                            })
                                                .catch(function(error) {
                                                    console.log(error);
                                                });
                            }
                            </script>
        </div>
    </div>
    @include('layouts.right')
</div> 
@endsection