

@extends('layouts.app')
@section('main')

<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <style>
                    .bg_img{
                        height: 400px;
                        background-position: center;
                        background-size: contain;
                    }
                    </style>
                <div class="owl-carousel carousel-banner owl-theme">
                    <div class="item">
                        <div class="bg_img" style="background-image: url({{asset('images/fogg-clip.png')}})"></div>
                    </div>
                    <div class="item">
                        <div class="bg_img" style="background-image: url({{asset('images/fogg-clip.png')}})"></div>
                    </div>
                    <div class="item">
                        <div class="bg_img" style="background-image: url({{asset('images/fogg-clip.png')}})"></div>
                    </div>
                    <div class="item">
                        <div class="bg_img" style="background-image: url({{asset('images/fogg-clip.png')}})"></div>
                    </div>
                    <div class="item">
                        <div class="bg_img" style="background-image: url({{asset('images/fogg-clip.png')}})"></div>
                    </div>
                    <div class="item">
                        <div class="bg_img" style="background-image: url({{asset('images/fogg-clip.png')}})"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 pt-2">
                <h2 class="fw-400 font-lg">{{ __('message.category')}} <a href="#" class="float-right" ></a></h2>
            </div>

            <div class="col-lg-12 mt-3">
                <div class="owl-carousel category-card owl-theme overflow-hidden overflow-visible-xl nav-none">
                    @forelse ($categories as $category)
                    <div  class="item categoryItems" onclick="choosecategory(this,{{$category->id}})">
                        <div class="card mr-1 w140 border-0 p-1 rounded-lg text-center" style="background-color: #fcf1eb;">
                            <div class="card-body p-2 ml-1 ">
                                <a  class="btn-round-xl bg-white"><img style="border-radius: 50%;
                                    height: 60px;
                                    width: 60px;" src="https://api.895773-cx81958.tmweb.ru/files/category/{{$language? (isset(json_decode($category->categoryIcon)->ru)? json_decode($category->categoryIcon)->ru : '') : (isset(json_decode($category->categoryIcon)->uz)? json_decode($category->categoryIcon)->uz : '')}}" alt="icon" class="p-2"></a>
                                <h4 class="fw-600 font-xsss mt-3 mb-0" style="height: 60px">{{$language?  (isset(json_decode($category->categoryName)->ru)? json_decode($category->categoryName)->ru : '') : (isset(json_decode($category->categoryName)->uz)? json_decode($category->categoryName)->uz : '')}} <span class="d-block font-xsssss fw-500 text-grey-500 mt-2">{{$category->courses->count()}} {{ __('message.coursel')}}</span></h4>
                            </div>
                        </div>
                    </div>
                    @empty
                        
                    @endforelse
                   


                      
                </div>
            </div>

            <div class="col-lg-12 pt-5 mb-3">
                <h2 class="fw-400 font-lg d-block">{{ __('message.courses')}}<a href="#" class="float-right" ></a></h2>
            </div>

            <div class="col-lg-12 pt-2 pb-4">
                <div class="owl-carousel category-card owl-theme overflow-hidden overflow-visible-xl nav-none">
                    <style>
                      .video-bttn:after{
                            content:none;
                        }
                        .font-xss{
                            display: -webkit-box!important;
                            display: -ms-flexbox!important;
                            display: flex!important;
                            -webkit-box-align: center;
                                -ms-flex-align: center;
                                    align-items: center;
                        }
                        .font-xss .font-xsssss{
                            margin-right: 5px;
                        }
                        .categoryItems{
                            cursor:pointer;
                        }
                        .card{
                            transition: all 0.5s ease;
                        }
                        .categoryItems:hover .card{
                                
                                box-shadow: 1px 1px 10px rgb(220 220 220);
                        }
                        .categoryItems .card.negative{
                            opacity: 0.3;
                        }
                        .categoryItems .card.active{
                            box-shadow: 1px 1px 10px rgb(220 220 220);
                        }
                        .courses.hide{
                            display:none;
                        }
                    </style>
                    @forelse ($courses as $course)
                    <div class="item courses cat-{{$course->category_id}}" >
                        <div class="card w300 p-0 shadow-xss  border-0 rounded-lg overflow-hidden mr-1 mb-4">
                            <div class="card-image w-100 mb-3">
                                <a href="{{route('course.web',['id'=>$course->id])}}" class="video-bttn position-relative d-block">
                                    @if($language)
                                        @if(isset(json_decode($course->getinfo->courseBanner)->uz))
                                            <img src="https://api.895773-cx81958.tmweb.ru/files/course/{{json_decode($course->getinfo->courseBanner)->ru}}" alt="image" class="w-100">
                                        @endif
                                    @else
                                        @if(isset(json_decode($course->getinfo->courseBanner)->uz))
                                            <img src="https://api.895773-cx81958.tmweb.ru/files/course/{{json_decode($course->getinfo->courseBanner)->uz}}" alt="image" class="w-100">
                                        @endif
                                    @endif
                                </a>
                            </div>
                            
                            <div class="card-body pt-0" style="height: 220px;">
                                <span style="font-size: 9px!important;
    padding: 0px 5px!important;"  class="font-xsssss fw-700 pl-3 pr-3 lh-32 text-uppercase rounded-lg ls-2 alert-warning d-inline-block text-warning mr-1">{{$language? (isset(json_decode($course->category->categoryName)->ru)? json_decode($course->category->categoryName)->ru : '') : (isset(json_decode($course->category->categoryName)->uz)? json_decode($course->category->categoryName)->uz : '')}}</span>
                                    
                                <span class="font-xss fw-700 pl-3 pr-3 ls-2 lh-32 d-inline-block text-success float-right"><span  class="font-xsssss">
                                    <svg width="30" height="30" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="24" cy="24" r="24" fill="#FFF176"/>
                                    <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"/>
                                </svg></span>@if($course->coursePrice) <span style="color:#f2bc23">{{$course->coursePrice}}</span>  @else {{isset($course->lessons[0])? $course->lessons[0]->quizes?->prizeIQC : 0 }} @endif</span>
                                  
                                <h4 class="fw-700 font-xss mt-3 lh-28 mt-0"><a href="{{route('course.web',['id'=>$course->id])}}" class="text-dark text-grey-900">{{$language?  (isset(json_decode($course->getinfo->courseTitleName)->ru)? json_decode($course->getinfo->courseTitleName)->ru : '') : (isset(json_decode($course->getinfo->courseTitleName)->uz)? json_decode($course->getinfo->courseTitleName)->uz : '') }} </a></h4>
                                <h6 class="font-xssss text-grey-500 fw-600 ml-0 mt-2">{{ __('message.lessonscount')}} {{$course->lessons->count()}}  </h6>
                                <ul class="memberlist mt-3 mb-2 ml-0 d-block">
                                    
                                    <li class="pl-4 w-auto"><a href="#" class="fw-500 text-grey-500 font-xssss">13 {{ __('message.studentscount')}}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                        
                    @endforelse
                    
                   <script>
                       function choosecategory(e,id){
                           
                           if(e.querySelector('.card.active')){
                               document.querySelectorAll('.categoryItems .card').forEach(element => {
                                 element.classList.remove('negative');
                                
                                });
                                e.querySelector('.card').classList.remove('active');
                                document.querySelectorAll('.courses').forEach(element => {
                                    element.classList.remove('hide');
                                     element.classList.remove('active');
                                
                                });
                               
                               
                           }else{
                                document.querySelectorAll('.categoryItems .card').forEach(element => {
                                element.classList.add('negative');
                                
                                });
                                e.querySelector('.card').classList.remove('negative');
                                e.querySelector('.card').classList.add('active');
                                
                                
                                document.querySelectorAll('.courses').forEach(element => {
                                    element.classList.add('hide');
                                
                                });
                                document.querySelectorAll('.cat-'+id).forEach(eleme => {
                                    eleme.classList.remove('hide');
                                    eleme.classList.add('active');
                                });
                                // document.querySelector('.cat-'+id).classList.remove('hide');
                                // document.querySelector('.cat-'+id).classList.add('active');
                           }
                           
                       }
                   </script>
                    
                    
                </div>
            </div>

            

             

        </div>
    </div>
    @include('layouts.right')
</div>
@endsection