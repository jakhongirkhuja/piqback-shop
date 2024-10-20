@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left">
        <div class="row">
            <style>
                .openingbtn{
                    width:100%!important;
                    border-radius: 4px;
                }
                .changedbtn{
                    display: inline-block!important;
                    width: 152px!important;
                    margin: 0 auto;
                    text-align: center;
                    background-color: #007382!important;
                }
                
                .range{
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    margin-top: 15px;
                    justify-content: space-around;
                    margin-bottom: 15px;
                }
                .range__self{
                    width: 200px;
                    height: 8px;
                    background-color: #D9D9D9;
                    display: block;
                    position: relative;
                    overflow: hidden;
                    border-radius: 2px;
                }
                .range__self i{
                    display: block;
                    height: 8px;
                    
                    position: absolute;
                    left: 0;
                    top: 0;
                    background: #007382;
                }
                .range__percent{
                    font-size: 14px;
                    line-height: 17px;
                    color: #000000;
                    white-space: nowrap;
                }
            </style>
            @forelse ($courses as $course)
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                <div class="card mb-4 d-block w-100 shadow-xss rounded-lg pl-5 pr-5 pt-4 pb-4 border-0 text-center">
                    
                    <a href="{{route('course.web',['id'=>$course->id])}}" class="ml-auto mr-auto rounded-lg overflow-hidden  d-inline-block">
                        @if($language)
                                        @if(isset(json_decode($course->getinfo->courseBanner)->uz))
                                            <img src="https://api.895773-cx81958.tmweb.ru/files/course/{{json_decode($course->getinfo->courseBanner)->ru}}" alt="icon" class="p-0 w100 shadow-xss openingbtn">
                                        @endif
                                    @else
                                        @if(isset(json_decode($course->getinfo->courseBanner)->uz))
                                            <img src="https://api.895773-cx81958.tmweb.ru/files/course/{{json_decode($course->getinfo->courseBanner)->uz}}" alt="icon" class="p-0 w100 shadow-xss openingbtn">
                                        @endif
                                    @endif
                       
                    </a>
                   
                    <p class="fw-600 font-xs  mb-1 text-center" style="font-size: 16px!important; line-height: 1;">{{$language? json_decode($course->getinfo->courseTitleName)->ru : json_decode($course->getinfo->courseTitleName)->uz}}</p>
                   
                    <div class="clearfix"></div>
                    <div class="range">
                        <div class="range__percent">40 %</div>
                        <div class="range__self"><i style="width:40%"></i></div>
                        <div class="range__finish"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3 0.5C3 0.367392 3.05268 0.240215 3.14645 0.146447C3.24021 0.0526785 3.36739 0 3.5 0H14.5C14.6326 0 14.7598 0.0526785 14.8536 0.146447C14.9473 0.240215 15 0.367392 15 0.5V1H17.5C17.6326 1 17.7598 1.05268 17.8536 1.14645C17.9473 1.24021 18 1.36739 18 1.5V4.5C18 5.16304 17.7366 5.79893 17.2678 6.26777C16.7989 6.73661 16.163 7 15.5 7H14.6585C13.888 9.18 11.8935 10.782 9.5 10.9795V14H13C13.1326 14 13.2598 14.0527 13.3536 14.1464C13.4473 14.2402 13.5 14.3674 13.5 14.5V17.5C13.5 17.6326 13.4473 17.7598 13.3536 17.8536C13.2598 17.9473 13.1326 18 13 18H5C4.86739 18 4.74021 17.9473 4.64645 17.8536C4.55268 17.7598 4.5 17.6326 4.5 17.5V14.5C4.5 14.3674 4.55268 14.2402 4.64645 14.1464C4.74021 14.0527 4.86739 14 5 14H8.5V10.9795C6.107 10.782 4.112 9.18 3.3415 7H2.5C1.83696 7 1.20107 6.73661 0.732233 6.26777C0.263392 5.79893 0 5.16304 0 4.5V1.5C0 1.36739 0.0526785 1.24021 0.146447 1.14645C0.240215 1.05268 0.367392 1 0.5 1H3V0.5ZM14 5V1H4V5C4 7.7615 6.2385 10 9 10C11.7615 10 14 7.7615 14 5ZM15 2V6H15.5C15.8978 6 16.2794 5.84196 16.5607 5.56066C16.842 5.27936 17 4.89782 17 4.5V2H15ZM1 2H3V6H2.5C2.10218 6 1.72064 5.84196 1.43934 5.56066C1.15804 5.27936 1 4.89782 1 4.5V2ZM5.5 15V17H12.5V15H5.5Z" fill="#F2C94C"/>
                            </svg></div>
                        
                    </div>
                    <a href="{{route('course.web',['id'=>$course->id])}}" class="changedbtn mt-3 p-0 btn p-2 lh-24 w100 ml-1 ls-3 d-inline-block rounded-xl bg-current font-xsssss fw-700 ls-lg text-white">Продолжить <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.3536 12.3536C22.5488 12.1583 22.5488 11.8417 22.3536 11.6464L19.1716 8.46447C18.9763 8.2692 18.6597 8.2692 18.4645 8.46447C18.2692 8.65973 18.2692 8.97631 18.4645 9.17157L21.2929 12L18.4645 14.8284C18.2692 15.0237 18.2692 15.3403 18.4645 15.5355C18.6597 15.7308 18.9763 15.7308 19.1716 15.5355L22.3536 12.3536ZM2 12.5H22V11.5H2V12.5Z" fill="white"/>
                        </svg></a>
                </div>
            </div>
            @empty
                        
                    @endforelse
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                <div class="card mb-4 d-block w-100 shadow-xss rounded-lg pl-5 pr-5 pt-4 pb-4 border-0 text-center">
                    
                    <a href="#" class="ml-auto mr-auto rounded-lg overflow-hidden  d-inline-block">
                        <img src="images/ne.jpg" alt="icon" class="p-0 w100 shadow-xss openingbtn">
                    </a>
                   
                    <p class="fw-600 font-xs  mb-1 text-center" style="font-size: 16px!important;">Название Курса </p>
                   
                    <div class="clearfix"></div>
                    <div class="range">
                        <div class="range__percent">100 %</div>
                        <div class="range__self"><i style="width:100%"></i></div>
                        <div class="range__finish"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3 0.5C3 0.367392 3.05268 0.240215 3.14645 0.146447C3.24021 0.0526785 3.36739 0 3.5 0H14.5C14.6326 0 14.7598 0.0526785 14.8536 0.146447C14.9473 0.240215 15 0.367392 15 0.5V1H17.5C17.6326 1 17.7598 1.05268 17.8536 1.14645C17.9473 1.24021 18 1.36739 18 1.5V4.5C18 5.16304 17.7366 5.79893 17.2678 6.26777C16.7989 6.73661 16.163 7 15.5 7H14.6585C13.888 9.18 11.8935 10.782 9.5 10.9795V14H13C13.1326 14 13.2598 14.0527 13.3536 14.1464C13.4473 14.2402 13.5 14.3674 13.5 14.5V17.5C13.5 17.6326 13.4473 17.7598 13.3536 17.8536C13.2598 17.9473 13.1326 18 13 18H5C4.86739 18 4.74021 17.9473 4.64645 17.8536C4.55268 17.7598 4.5 17.6326 4.5 17.5V14.5C4.5 14.3674 4.55268 14.2402 4.64645 14.1464C4.74021 14.0527 4.86739 14 5 14H8.5V10.9795C6.107 10.782 4.112 9.18 3.3415 7H2.5C1.83696 7 1.20107 6.73661 0.732233 6.26777C0.263392 5.79893 0 5.16304 0 4.5V1.5C0 1.36739 0.0526785 1.24021 0.146447 1.14645C0.240215 1.05268 0.367392 1 0.5 1H3V0.5ZM14 5V1H4V5C4 7.7615 6.2385 10 9 10C11.7615 10 14 7.7615 14 5ZM15 2V6H15.5C15.8978 6 16.2794 5.84196 16.5607 5.56066C16.842 5.27936 17 4.89782 17 4.5V2H15ZM1 2H3V6H2.5C2.10218 6 1.72064 5.84196 1.43934 5.56066C1.15804 5.27936 1 4.89782 1 4.5V2ZM5.5 15V17H12.5V15H5.5Z" fill="#F2C94C"/>
                            </svg></div>
                        
                    </div>
                    <a href="#" class="changedbtn mt-3 p-0 btn p-2 lh-24 w100 ml-1 ls-3 d-inline-block rounded-xl bg-current font-xsssss fw-700 ls-lg text-white">Пересмотреть <svg class="ml-2" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.67695 19.5669C1.53095 17.0209 -0.242051 11.7579 1.71695 7.14388C3.87495 2.05988 9.74495 -0.312121 14.8289 1.84588C19.9129 4.00388 22.2849 9.87488 20.1269 14.9589C19.3189 16.8703 17.9337 18.4814 16.1649 19.5669" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 15V19.4C16 19.5591 16.0632 19.7117 16.1757 19.8243C16.2883 19.9368 16.4409 20 16.6 20H21M11 21.01L11.01 20.999" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        </a>
                </div>
            </div>


             
        </div>
    </div>
    @include('layouts.right')
</div>  
@endsection