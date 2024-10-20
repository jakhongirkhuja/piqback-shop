@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left">
        <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden mb-3" style="background-image: url(images/cover.jpg);">
            <div class="card-body p-lg-1 p-1 bg-black-08">
                <div class="clearfix"></div>
                <div class="row p-md-2">
                    
                    <div class="col-lg-12 pl-xl-5 pt-xl-5">
                        <figure class="avatar ml-0 mb-4 position-relative w100 z-index-1">
                            <div class="avatar__new_user {{!$user->gender? 'av__man' : 'av__girl'}} 
                            @php if($user->role=='Employee'){
                                  
                                    if($user->gender){
                                            echo 'employeeshe';
                                    }else{
                                    
                                        echo 'employeehe';
                                    }
                                }else{
                               
                                    if($user->gender){
                                            echo 'companyownershe';
                                    }else{
                                    
                                        echo 'companyownerhe';
                                    }
                                }

                            
                            @endphp rounded-circle">
                            
                            </div>
                        </figure>
                    </div>
                    <div class="col-xl-12 col-lg-12 pl-xl-5 pb-xl-1 pb-1">
                        
                        <h4 class="fw-700 font-md text-white mt-3 mb-1">{{$user->firstName}} {{$user->lastName}}</h4>
                      
                        <span class="font-xssss fw-600 text-grey-500 d-inline-block ml-0">{{$email? $email->email : ''}}</span>
                        
                    </div>
                    <div class="col-xl-12 col-lg-12 d-block pl-xl-5 ">
                        <h2 class="display5-size text-white fw-700 lh-1 mr-3" style="display: -webkit-box; display: -ms-flexbox; display: flex; gap:15px">@if( $iqc){{$iqc->amountofIQC}} @else 0 @endif <svg width="58" height="58" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="24" fill="#FFF176"/>
                            <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"/>
                        </svg></h2>
                        <h4 class="text-white font-sm fw-600 mt-0 lh-3">{{$language? 'Баланс' : 'Balans' }} </h4>

                    </div>
                    
                </div>
                         
                     <style>
                                button, [type=button], [type=reset], [type=submit] {
                                    -webkit-appearance: button-bevel;
                                    }
                               </style>

            </div>
        </div>
        <style>
            .hero__form--body {
                width: 240px;
                }

                @media screen and (max-width: 700px) {
                .hero__form--body {
                    margin-top: 40px;
                    margin: 0 auto;
                 }
                 .p-md-2{
                     padding:10px;
                 }
                }

                .hero__form--body .title {
                font-weight: 600;
                font-size: 32px;
                line-height: 39px;
                color: #000000;
                text-align: center;
                margin-bottom: 8vh;
                }

                .hero__form--body div.error {
                font-weight: 600;
                font-size: 16px;
                line-height: 20px;
                color: #FF736E;
                margin-bottom: 3.5vh;
                }

                .hero__form--body div.error.info {
                color: #000000;
                }

                .hero__form--body .form__block {
                display: none;
                }

                .hero__form--body .form__block .map_itself {
                height: 260px;
                }

                .hero__form--body .form__block.active {
                display: block;
                }

                .hero__form--body .privacy {
                /* Hide the browser's default checkbox */
                /* Create a custom checkbox */
                /* On mouse-over, add a grey background color */
                /* When the checkbox is checked, add a blue background */
                /* Create the checkmark/indicator (hidden when not checked) */
                /* Show the checkmark when checked */
                /* Style the checkmark/indicator */
                }

                .hero__form--body .privacy .container {
                display: block;
                position: relative;
                padding-left: 23px;
                color: #666666;
                font-weight: 600;
                font-size: 10px;
                line-height: 12px;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                }

                .hero__form--body .privacy .container input {
                position: absolute;
                opacity: 0;
                cursor: pointer;
                height: 0;
                width: 0;
                }

                .hero__form--body .privacy a {
                color: #4B96DC;
                }

                .hero__form--body .privacy .checkmark {
                position: absolute;
                top: 0;
                left: 0;
                height: 16px;
                width: 16px;
                cursor: pointer;
                border: 1px solid #2D3232;
                }

                .hero__form--body .privacy .container:hover input ~ .checkmark {
                background-color: #4B96DC;
                }

                .hero__form--body .privacy .container input:checked ~ .checkmark {
                background-color: #4DB1B1;
                }

                .hero__form--body .privacy .checkmark:after {
                content: "";
                position: absolute;
                display: none;
                }

                .hero__form--body .privacy .container input:checked ~ .checkmark:after {
                display: block;
                }

                .hero__form--body .privacy .container .checkmark:after {
                left: 5px;
                top: 1px;
                width: 2px;
                height: 7px;
                border: solid white;
                border-width: 0 3px 3px 0;
                -webkit-transform: rotate(45deg);
                transform: rotate(45deg);
                }

                .hero__form--body .form__wrap {
                margin-bottom: 2vh;
                position: relative;
                }

                .hero__form--body .form__wrap svg {
                position: absolute;
                left: 23px;
                top: 50%;
                -webkit-transform: translate(0, -50%);
                        transform: translate(0, -50%);
                }

                .hero__form--body .form__wrap label {
                font-weight: 600;
                font-size: 16px;
                line-height: 20px;
                color: #A1A1A1;
                }

                .hero__form--body .form__wrap input:focus ~ label,
                .hero__form--body .form__wrap input:not(:focus):valid ~ label {
                position: absolute;
                left: 1px;
                top: -6px;
                font-size: 12px;
                line-height: 15px;
                opacity: 1;
                }

                .hero__form--body .form__wrap input ~ label {
                position: absolute;
                pointer-events: none;
                -webkit-transition: 0.2s ease all;
                transition: 0.2s ease all;
                left: 54px;
                top: 50%;
                -webkit-transform: translate(0, -50%);
                        transform: translate(0, -50%);
                }

                .hero__form--body .form__wrap input {
                padding-left: 55px;
                }

                .hero__form--body .form__wrap input:focus {
                border-color: #4B96DC;
                color: #4B96DC;
                }

                .hero__form--body .form__wrap input:focus ~ label {
                color: #4B96DC;
                }

                .hero__form--body .form__wrap input:focus ~ svg path {
                fill: #4B96DC;
                }

                .hero__form--body .form__wrap input:not(:focus):valid {
                border-color: #4DB1B1;
                color: #4DB1B1;
                }

                .hero__form--body .form__wrap input:not(:focus):valid ~ label {
                color: #4DB1B1;
                }

                .hero__form--body .form__wrap input:not(:focus):valid ~ svg path {
                fill: #4DB1B1;
                }

                .hero__form--body .form__wrap input:focus.error, .hero__form--body .form__wrap input:not(:focus):valid.error, .hero__form--body .form__wrap input.error {
                border-color: #FF736E;
                color: #FF736E;
                }

                .hero__form--body .form__wrap input:focus.error ~ label, .hero__form--body .form__wrap input:not(:focus):valid.error ~ label, .hero__form--body .form__wrap input.error ~ label {
                color: #FF736E;
                }

                .hero__form--body .form__wrap input:focus.error ~ svg path, .hero__form--body .form__wrap input:not(:focus):valid.error ~ svg path, .hero__form--body .form__wrap input.error ~ svg path {
                fill: #FF736E;
                }

                .hero__form--body .form__wrap.iconno input {
                padding-left: 20px;
                padding-right: 32px;
                }

                .hero__form--body .form__wrap.iconno input ~ label {
                left: 20px;
                top: 50%;
                -webkit-transform: translate(0, -50%);
                        transform: translate(0, -50%);
                }

                .hero__form--body .form__wrap.iconno input:focus ~ label,
                .hero__form--body .form__wrap.iconno input:not(:focus):valid ~ label {
                position: absolute;
                left: 1px;
                top: -6px;
                font-size: 12px;
                line-height: 15px;
                opacity: 1;
                }

                .hero__form--body .form__wrap.iconno .drop {
                position: absolute;
                right: 20px;
                top: 50%;
                -webkit-transform: translate(0, -50%);
                        transform: translate(0, -50%);
                }

                .hero__form--body .form__wrap.rightone svg {
                position: absolute;
                right: 13px;
                left: initial;
                top: 50%;
                -webkit-transform: translate(0, -50%);
                        transform: translate(0, -50%);
                }

                .hero__form--body .form__wrap--radio {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -webkit-box-pack: justify;
                    -ms-flex-pack: justify;
                        justify-content: space-between;
                -webkit-box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.25);
                        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.25);
                border-radius: 30px;
                height: 44px;
                padding: 0 16px;
                -webkit-box-align: center;
                    -ms-flex-align: center;
                        align-items: center;
                }

                .hero__form--body .form__wrap--radio .radio__text {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                color: #C4C4C4;
                font-weight: 600;
                font-size: 12px;
                line-height: 0;
                -webkit-box-align: center;
                    -ms-flex-align: center;
                        align-items: center;
                -webkit-transition: all .5s ease;
                transition: all .5s ease;
                }

                .hero__form--body .form__wrap--radio .radio__text svg {
                position: relative;
                margin-right: 10px;
                left: 0;
                top: 9px;
                }

                .hero__form--body .form__wrap--radio .radio__text svg path {
                fill: #C4C4C4;
                }

                .hero__form--body .form__wrap--radio .radio__button {
                width: 50%;
                background-color: #45A9D3;
                -webkit-box-shadow: 2px 2px 4px rgba(69, 169, 211, 0.25);
                        box-shadow: 2px 2px 4px rgba(69, 169, 211, 0.25);
                border-radius: 30px;
                height: 114%;
                position: absolute;
                z-index: 0;
                -webkit-transition: all .3s ease;
                transition: all .3s ease;
                }

                .hero__form--body .form__wrap--radio .radio__button.man {
                left: -1%;
                -webkit-box-shadow: 2px 2px 4px rgba(69, 169, 211, 0.25);
                        box-shadow: 2px 2px 4px rgba(69, 169, 211, 0.25);
                background-color: #45A9D3;
                }

                .hero__form--body .form__wrap--radio .radio__button.woman {
                left: 51%;
                background-color: #FF038E;
                -webkit-box-shadow: 2px 2px 8px rgba(255, 3, 142, 0.25);
                        box-shadow: 2px 2px 8px rgba(255, 3, 142, 0.25);
                }

                .hero__form--body .form__wrap--radio_each {
                position: relative;
                z-index: 1;
                height: 100%;
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                cursor: pointer;
                }

                .hero__form--body .form__wrap--radio_each.active .radio__text {
                color: #E6F0F0;
                }

                .hero__form--body .form__wrap--radio_each.active svg path {
                fill: #E6F0F0;
                }

                .hero__form--body .form__wrap--radio.general {
                margin-bottom: 35px;
                height: 40px;
                }

                .hero__form--body .form__wrap--radio.general.map {
                margin-bottom: 20px;
                }

                .hero__form--body .form__wrap--radio.general .form__wrap--radio_each.active .radio__text {
                color: #4B96DC;
                }

                .hero__form--body .form__wrap--radio.general .radio__button {
                -webkit-box-shadow: 2px 2px 4px rgba(75, 150, 220, 0.25);
                        box-shadow: 2px 2px 4px rgba(75, 150, 220, 0.25);
                }

                .hero__form--body .form__wrap--radio.general .radio__button.left {
                left: -1%;
                background-color: #FFFFFF;
                }

                .hero__form--body .form__wrap--radio.general .radio__button.right {
                left: 45%;
                width: 58%;
                background-color: #FFFFFF;
                }

                .hero__form--body .form__wrap--radio.general.map {
                padding: 0 26px;
                }

                .hero__form--body .form__wrap--radio.general.map .radio__button {
                -webkit-box-shadow: 2px 2px 4px rgba(75, 150, 220, 0.25);
                        box-shadow: 2px 2px 4px rgba(75, 150, 220, 0.25);
                }

                .hero__form--body .form__wrap--radio.general.map .radio__button.left {
                left: -1%;
                background-color: #FFFFFF;
                }

                .hero__form--body .form__wrap--radio.general.map .radio__button.right {
                left: 51%;
                width: 50%;
                background-color: #FFFFFF;
                }

                .hero__form--body .form__wrap .dropbox__block {
                display: none;
                position: absolute;
                z-index: 1;
                left: 50%;
                -webkit-transform: translate(-50%, 0);
                        transform: translate(-50%, 0);
                width: 220px;
                background: #FFFFFF;
                -webkit-box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.15);
                        box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.15);
                }

                .hero__form--body .form__wrap .dropbox__block--each {
                height: 40px;
                padding: 10px;
                cursor: pointer;
                color: #000000;
                }

                .hero__form--body .form__wrap .dropbox__block--each:hover {
                background-color: #EDEDED;
                }

                .hero__form--body .form__wrap .dropbox__block.active {
                display: block;
                }

                .hero__form--body .forget {
                text-align: right;
                font-weight: 600;
                font-size: 14px;
                line-height: 17px;
                color: #c4c4c4;
                margin-bottom: 7px;
                }

                .hero__form--body .btn img {
                margin-left: 19px;
                }
                .hero__form--body .btn-secondary{
                    background: #007382;
                    border-radius: 5px;
                    width: 100%;
                    padding: 12px 0;
                }
                .changedui{
                    font-weight: 600;
                    font-size: 14px;
                    line-height: 15px;
                    color: #000000;
                    display: flex;
                    gap: 5px;
                    align-items: center;
                    cursor: pointer;
                }
                
        </style>
        <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden mb-4">
            <ul class="nav nav-tabs xs-p-4 d-flex align-items-center justify-content-between product-info-tab border-bottom-0" id="pills-tab" role="tablist">
                <li class="active list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 ml-sm-5 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase active" href="#navtabs0" data-toggle="tab">{{ __('message.profile')}}</a></li>
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase" href="#navtabs1" data-toggle="tab">{{ __('message.phonenumber')}}</a></li>
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase" href="#navtabs2" data-toggle="tab">{{ __('message.password')}}</a></li>
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase" href="#navtabs3" data-toggle="tab">{{ __('message.email')}}</a></li>
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase" href="#navtabs4" data-toggle="tab">{{ __('message.reflink')}}</a></li>
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase {{auth()->user()?->role!='Company Owner'? 'pr-3' : ''}} " href="#navtabs5" data-toggle="tab">{{ __('message.myiqc')}}</a></li>
                 @if(auth()->user()?->role=='Company Owner')
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase" href="#navtabs6" data-toggle="tab">{{ __('message.mypharmacy')}}</a></li>
                <li class="list-inline-item"><a class="fw-700 pb-sm-5 pt-sm-5 xs-mb-2 font-xssss text-grey-500 ls-3 d-inline-block text-uppercase pr-2" href="#navtabs7" data-toggle="tab">{{ __('message.myteam')}}</a></li>
                @endif
            </ul>
        </div>
        <div class="tab-content" id="pills-tabContent">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="width: 100%">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(Session::has('message'))
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                @endif
            <div class="tab-pane fade show active" id="navtabs0">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block">  <b>{{ __('message.profile')}}</b> <div onclick="startedit('infobtn','forminfo'); " class="changedui float-right">{{ __('message.save')}} <i class="feather-edit text-grey-500 font-xs"></i></div></h2></div>
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="hero__form--body">
                                   <style>
                                      
                                   </style>
                                    <form action="{{route('profile.info')}}" class="forminfo" method="post">
                                        @csrf
                                      <div class="form__wrap icon">
                                        <input type="text" class="form-control" onkeydown="return false;"   name="firstName" value="{{$user->firstName}}" required="">
                                        <label>{{ __('message.name')}}</label>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M8 2C9.1 2 10 2.9 10 4C10 5.1 9.1 6 8 6C6.9 6 6 5.1 6 4C6 2.9 6.9 2 8 2ZM8 12C10.7 12 13.8 13.29 14 14H2C2.23 13.28 5.31 12 8 12ZM8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0ZM8 10C5.33 10 0 11.34 0 14V16H16V14C16 11.34 10.67 10 8 10Z" fill="#A1A1A1"></path>
                                          </svg>
                                      </div>
                                      
                                      <div class="form__wrap icon">
                                        <input type="text" class="form-control" onkeydown="return false;"  name="lastName" value="{{$user->lastName}}"  required="">
                                        <label>{{ __('message.familyname')}}</label>
                                        <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M7 8.75C4.66 8.75 0 9.92 0 12.25V14H14V12.25C14 9.92 9.34 8.75 7 8.75ZM2.34 12C3.18 11.42 5.21 10.75 7 10.75C8.79 10.75 10.82 11.42 11.66 12H2.34ZM7 7C8.93 7 10.5 5.43 10.5 3.5C10.5 1.57 8.93 0 7 0C5.07 0 3.5 1.57 3.5 3.5C3.5 5.43 5.07 7 7 7ZM7 2C7.83 2 8.5 2.67 8.5 3.5C8.5 4.33 7.83 5 7 5C6.17 5 5.5 4.33 5.5 3.5C5.5 2.67 6.17 2 7 2ZM14.04 8.81C15.2 9.65 16 10.77 16 12.25V14H20V12.25C20 10.23 16.5 9.08 14.04 8.81ZM13 7C14.93 7 16.5 5.43 16.5 3.5C16.5 1.57 14.93 0 13 0C12.46 0 11.96 0.13 11.5 0.35C12.13 1.24 12.5 2.33 12.5 3.5C12.5 4.67 12.13 5.76 11.5 6.65C11.96 6.87 12.46 7 13 7Z" fill="#A1A1A1"></path>
                                          </svg>                  
                                      </div>
                                      <div class="form__wrap icon ">
                                        <input type="text" class="form-control" onkeydown="return false;"  name="birthDate" value="{{Carbon\Carbon::parse($user->birthDate)->format('Y-m-d')}}" required="">
                                        <label>{{ __('message.birthday')}}</label>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M11 12H17V18H11V12Z" fill="#C4C4C4"></path>
                                          <path d="M19 4H17V2H15V4H9V2H7V4H5C3.897 4 3 4.897 3 6V20C3 21.103 3.897 22 5 22H19C20.103 22 21 21.103 21 20V6C21 4.897 20.103 4 19 4ZM19.001 20H5V8H19L19.001 20Z" fill="#C4C4C4"></path>
                                          </svg>                                  
                                      </div>
                                      <div class="form__wrap mt-1">
                                        <div class="form__wrap--radio">
                                            <div class="form__wrap--radio_each {{!$user->gender? 'active' : ''}}" onclick="chooseGender(this,0 )">
                                                <div class="radio__text">
                                                    <svg width="8" height="20" viewBox="0 0 8 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6 5H2C0.9 5 0 5.9 0 7V13H2V20H6V13H8V7C8 5.9 7.1 5 6 5Z" fill="#E6F0F0"></path>
                                                        <path d="M4 4C5.10457 4 6 3.10457 6 2C6 0.89543 5.10457 0 4 0C2.89543 0 2 0.89543 2 2C2 3.10457 2.89543 4 4 4Z" fill="#E6F0F0"></path>
                                                    </svg>
                                                    <span>{{ __('message.man')}}</span>
                                                </div>
                                            </div>
                                            <div class="form__wrap--radio_each {{$user->gender? 'active' : ''}}" onclick="chooseGender(this,1 )">
                                                <div class="radio__text">
                                                    <svg width="10" height="20" viewBox="0 0 10 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.94 6.31C6.62 5.52 5.85 5 5 5C4.15 5 3.38 5.52 3.06 6.31L0 14H3V20H7V14H10L6.94 6.31Z" fill="#C4C4C4"></path>
                                                        <path d="M5 4C6.10457 4 7 3.10457 7 2C7 0.89543 6.10457 0 5 0C3.89543 0 3 0.89543 3 2C3 3.10457 3.89543 4 5 4Z" fill="#C4C4C4"></path>
                                                    </svg>
                                                    <span>{{ __('message.woman')}}</span>
                                                </div>
                                            </div>
                                                <input type="hidden" id="gender2" name="gender2">
                                            <div class="radio__button {{!$user->gender? 'man' : 'woman'}}  "></div></div>
                                        <script>
                                          function chooseGender(e, gender){
                                            document.querySelector('#gender2').value = gender;
                                            document.querySelectorAll('.form__wrap--radio_each').forEach(element => {
                                              element.classList.remove('active');
                                            });
                                            e.classList.add('active');
                                            let radiobtn = document.querySelector('.radio__button');
                                            radiobtn.classList.remove('man', 'woman');
                                            if(gender){
                                              radiobtn.classList.add('woman');
                                            }else{
                                              radiobtn.classList.add('man');
                                            }
                                          }
                                        </script>
                                                                          
                                      </div>
                                      <button id="infobtn" class="btn btn-secondary" style="display: none">
                                        <span>{{ __('message.save')}} <svg class="ml-1" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18.6667 0H2.66667C1.18667 0 0 1.2 0 2.66667V21.3333C0 22.8 1.18667 24 2.66667 24H21.3333C22.8 24 24 22.8 24 21.3333V5.33333L18.6667 0ZM12 21.3333C9.78667 21.3333 8 19.5467 8 17.3333C8 15.12 9.78667 13.3333 12 13.3333C14.2133 13.3333 16 15.12 16 17.3333C16 19.5467 14.2133 21.3333 12 21.3333ZM16 8H2.66667V2.66667H16V8Z" fill="white"/>
                                            </svg></span> 
                                      </button>
                                    </form>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="navtabs1">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block">{{ __('message.phonenumber')}} <div onclick="startedit('telbtn','formtebn')" class="float-right changedui">{{ __('message.change')}}<i class="feather-edit text-grey-500 font-xs"></i></div></h2></div>
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="hero__form--body">
                                    
                                    <form action="{{route('profile.phonebook')}}" class="formtebn" method="post">
                                    @csrf
                                      <div class="form__wrap icon">
                                        <input type="text" class="form-control" onkeydown="return false;" name="number" value="{{$user->phonebook->phoneNumber}}" required="">
                                        <label>{{ __('message.phone')}}</label>
                                        <input type="hidden" name="platform" value="website">
                                        <input type="hidden" name="device">
                                        <input type="hidden" name="browser">
                                        <input type="hidden" name="timeZone">
                                        <svg width="15" height="23" viewBox="0 0 15 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M12.5 0.51L2.5 0.5C1.4 0.5 0.5 1.4 0.5 2.5V20.5C0.5 21.6 1.4 22.5 2.5 22.5H12.5C13.6 22.5 14.5 21.6 14.5 20.5V2.5C14.5 1.4 13.6 0.51 12.5 0.51ZM12.5 18.5H2.5V4.5H12.5V18.5Z" fill="#A1A1A1"></path>
                                        </svg>
                                      </div>
                                      
                                      <button id="telbtn"  class="btn btn-secondary" style="display: none">
                                        <span>{{ __('message.save')}} <svg class="ml-1" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18.6667 0H2.66667C1.18667 0 0 1.2 0 2.66667V21.3333C0 22.8 1.18667 24 2.66667 24H21.3333C22.8 24 24 22.8 24 21.3333V5.33333L18.6667 0ZM12 21.3333C9.78667 21.3333 8 19.5467 8 17.3333C8 15.12 9.78667 13.3333 12 13.3333C14.2133 13.3333 16 15.12 16 17.3333C16 19.5467 14.2133 21.3333 12 21.3333ZM16 8H2.66667V2.66667H16V8Z" fill="white"/>
                                            </svg></span> 
                                      </button>
                                    </form>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="navtabs2">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block">{{ __('message.password')}} <div onclick="startedit('passbtn','formpass')" class="float-right changedui">{{ __('message.change')}}<i class="feather-edit text-grey-500 font-xs"></i></div></h2></div>
                    <div class="card-body pb-0">
                       <div class="row">
                        <div class="col-md-12">
                            <div class="hero__form--body">
                               
                                <form action="{{route('profile.profilePassword')}}" class="formpass" method="POST">
                                 @csrf
                                 
                                 <div class="form__wrap iconno rightone">
                                    <input type="password" class="form-control" onkeydown="return false;" name="password_old" required="">
                                    <label>{{ __('message.oldpassword')}}</label>
                                    <input type="hidden" name="platform" value="website">
                                        <input type="hidden" name="device">
                                        <input type="hidden" name="browser">
                                        <input type="hidden" name="timeZone">
                                  </div>
                                  <div class="form__wrap iconno rightone">
                                    <input type="password" class="form-control" onkeydown="return false;" name="password" required="">
                                    <label>{{ __('message.newpassword')}}</label>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M10.79 12.912L9.176 11.297C8.55184 11.5202 7.87715 11.5615 7.23042 11.4162C6.58369 11.2709 5.99153 10.9449 5.52282 10.4762C5.05411 10.0075 4.72814 9.41531 4.58283 8.76858C4.43752 8.12185 4.47885 7.44716 4.702 6.823L2.642 4.763C0.938 6.278 0 8 0 8C0 8 3 13.5 8 13.5C8.9604 13.4967 9.90994 13.2965 10.79 12.912ZM5.21 3.088C6.09005 2.70342 7.03959 2.50331 8 2.5C13 2.5 16 8 16 8C16 8 15.061 9.721 13.359 11.238L11.297 9.176C11.5202 8.55184 11.5615 7.87715 11.4162 7.23042C11.2709 6.58369 10.9449 5.99153 10.4762 5.52282C10.0075 5.05411 9.41531 4.72814 8.76858 4.58283C8.12185 4.43752 7.44716 4.47885 6.823 4.702L5.21 3.089V3.088Z" fill="#C4C4C4"></path>
                                      <path d="M5.52548 7.646C5.47048 8.03031 5.50573 8.42215 5.62845 8.79047C5.75117 9.15879 5.95798 9.49347 6.2325 9.76799C6.50701 10.0425 6.84169 10.2493 7.21001 10.372C7.57833 10.4948 7.97017 10.53 8.35448 10.475L5.52448 7.646H5.52548ZM10.4755 8.354L7.64648 5.524C8.03079 5.46899 8.42263 5.50424 8.79096 5.62696C9.15928 5.74968 9.49396 5.95649 9.76847 6.23101C10.043 6.50553 10.2498 6.8402 10.3725 7.20853C10.4952 7.57685 10.5305 7.96869 10.4755 8.353V8.354ZM13.6465 14.354L1.64648 2.354L2.35448 1.646L14.3545 13.646L13.6465 14.354Z" fill="#C4C4C4"></path>
                                      </svg>
                                  </div>
                                  <div class="form__wrap iconno rightone">
                                    <input type="password" name="password_confirmation" onkeydown="return false;" class="form-control" required="">
                                    <label>{{ __('message.confirmpassword')}}</label>
                                    
                                  </div>
                                  
                                  <button id="passbtn" class="btn btn-secondary" style="display: none">
                                    <span>{{ __('message.save')}} <svg class="ml-1" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.6667 0H2.66667C1.18667 0 0 1.2 0 2.66667V21.3333C0 22.8 1.18667 24 2.66667 24H21.3333C22.8 24 24 22.8 24 21.3333V5.33333L18.6667 0ZM12 21.3333C9.78667 21.3333 8 19.5467 8 17.3333C8 15.12 9.78667 13.3333 12 13.3333C14.2133 13.3333 16 15.12 16 17.3333C16 19.5467 14.2133 21.3333 12 21.3333ZM16 8H2.66667V2.66667H16V8Z" fill="white"/>
                                        </svg></span> 
                                  </button>
                                </form>
                              </div>
                        </div>
                       </div>
                    </div>  
                </div>
            </div>

            <div class="tab-pane fade" id="navtabs3">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block"><b>{{ __('message.mail')}}</b> <div onclick="startedit('emailbtn','emailform')"  class="float-right changedui">{{ __('message.change')}}<i class="feather-edit text-grey-500 font-xs"></i></div></h2></div>
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="hero__form--body">
                                    
                                    <form method="post" action="{{route('profile.profileEmail')}}" class="emailform">
                                        @csrf
                                      <div class="form__wrap iconno rightone">
                                        <input type="text" class="form-control" name="email" onkeydown="return false;" value="{{$email? $email->email : ''}}" required="">
                                        <label>{{ __('message.mail')}}</label>
                                          </div>
                                          <input type="hidden" name="platform" value="website">
                                          <input type="hidden" name="device">
                                          <input type="hidden" name="browser">
                                          <input type="hidden" name="timeZone">
                                      <button id="emailbtn" class="btn btn-secondary" style="display: none">
                                        <span>{{ __('message.save')}} <svg class="ml-1" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18.6667 0H2.66667C1.18667 0 0 1.2 0 2.66667V21.3333C0 22.8 1.18667 24 2.66667 24H21.3333C22.8 24 24 22.8 24 21.3333V5.33333L18.6667 0ZM12 21.3333C9.78667 21.3333 8 19.5467 8 17.3333C8 15.12 9.78667 13.3333 12 13.3333C14.2133 13.3333 16 15.12 16 17.3333C16 19.5467 14.2133 21.3333 12 21.3333ZM16 8H2.66667V2.66667H16V8Z" fill="white"/>
                                            </svg></span> 
                                      </button>
                                    </form>
                                  </div>
                            </div>
                            
                        </div>
                    </div>  
                </div>
            </div>

            <div class="tab-pane fade" id="navtabs4">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block">{{ __('message.reflink')}} </h2></div>
                    <div class="card-body pb-0">
                        <div class="row">
                            <style>
                                .copy__link{
                                    background: #4B96DC;
                                    font-size: 16px;
                                    font-weight: 400;
                                    color: #E6F0F0;
                                    border: 1px solid #E6F0F0;
                                    border-radius: 6px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    padding: 15px;
                                    cursor: pointer;
                                    position: relative;
                                    
                                }
                                .mobile{
                                    display:none;
                                }
                                @media only screen and (max-width: 600px) {
                                    .mobile{
                                    display:block;
                                }
                                    .copy__link{
                                        padding: 5px;
                                            font-size: 10px;
                                    }
                                    }
                                .copy__link:hover{
                                    opacity: 0.9;
                                }
                                .copy__link svg{
                                    position: relative;
                                }
                                .copy__link .tooltiptext {
                                    visibility: hidden;
                                    background-color: black;
                                    color: #fff;
                                    text-align: center;
                                    padding: 5px 10px;
                                    border-radius: 6px;
                                    position: absolute;
                                    z-index: 1;
                                    top: -41px;
                                    right: -25px;
                                    }
                                    .copy__link .tooltiptext::after{
                                        content: '';
                                        display: inline-block;
                                        width: 0;
                                        height: 0;
                                        border-style: solid;
                                        border-width: 11px 11px 0 11px;
                                        border-color: #000000 transparent transparent transparent;
                                        position: absolute;
                                        bottom: -8px;
                                        left: 50%;
                                        transform: translate(-50%, 0);
                                        
                                    }
                                    .copy__link:hover .tooltiptext {
                                    visibility: visible;
                                    }
                            </style>
                            <div class="col-md-6">
                                <p>{{ __('message.sendthislink')}}</p>
                                <div class="copy__link" onclick="myFunction()"  data-toggle="tooltip" title="Disabled tooltip" type="button"><span>https://go.pharmiq.uz/register/{{auth()->user()?->hrid}}</span> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4.9635V3.5C6 3.10218 6.15804 2.72064 6.43934 2.43934C6.72064 2.15804 7.10218 2 7.5 2H20.5C20.8978 2 21.2794 2.15804 21.5607 2.43934C21.842 2.72064 22 3.10218 22 3.5V16.5C22 16.8978 21.842 17.2794 21.5607 17.5607C21.2794 17.842 20.8978 18 20.5 18H19.0085" stroke="#E6F0F0"/>
                                    <path d="M17.5 5H3.5C2.67157 5 2 5.67157 2 6.5V20.5C2 21.3284 2.67157 22 3.5 22H17.5C18.3284 22 19 21.3284 19 20.5V6.5C19 5.67157 18.3284 5 17.5 5Z" stroke="#E6F0F0" stroke-linejoin="round"/>
                                    <path d="M9.21996 11.5551L11.866 8.80009C12.5915 8.07459 13.7845 8.09009 14.53 8.83609C15.2755 9.58159 15.2915 10.7746 14.566 11.5001L13.611 12.5116M6.73296 14.3736C6.47796 14.6286 5.95046 15.1386 5.95046 15.1386C5.22446 15.8641 5.20446 17.1576 5.95046 17.9036C6.69546 18.6486 7.88846 18.6651 8.61446 17.9391L11.1965 15.5951" stroke="#E6F0F0" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.33161 14.164C8.99947 13.834 8.79851 13.3946 8.76611 12.9275C8.74695 12.6655 8.78437 12.4024 8.87581 12.1561C8.96726 11.9098 9.1106 11.686 9.29611 11.5M11.1606 12.9305C11.9061 13.676 11.9221 14.869 11.1966 15.595" stroke="#E6F0F0" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg> 
                                    <span class="tooltiptext">{{ __('message.copy')}}</span>
                                </div>
                                <div class="text-center mt-3">{{ __('message.registered')}}: 0</div>
                                <input type="text" style="display:none" value="https://go.pharmiq.uz/register/{{auth()->user()?->hrid}}" id="myInput">
                               
<!--<input type="text" value="Hello World" id="myInput">-->
<!--<button onclick="myFunction()">Copy text</button>-->

<script>
function myFunction() {
  /* Get the text field */
  var copyText = document.getElementById("myInput");

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */

  /* Copy the text inside the text field */
  navigator.clipboard.writeText(copyText.value);
  document.querySelector('.tooltiptext').innerHTML = '{{ __('message.copied')}}';
  setTimeout(() => {
    document.querySelector('.tooltiptext').innerHTML = '{{ __('message.copy')}}';
  }, 3000);
  
  /* Alert the copied text */
//   alert("Copied the text: " + copyText.value);
}
</script>
                                    
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="tab-pane fade" id="navtabs5">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0 row">
                        <div class="col-md-8">
                            <h2 class="fw-400 font-lg d-block">
                                {{ __('message.myiqc')}}
                            </h2>
                        </div>
                        <style>
                            .iqc{
                                font-size: 24px;
                                color: #007382;
                            }
                            svg{
                                margin-right: 5px;
                            }
                        </style>
                        <div class="col-md-4 text-right">
                            <div class="iqc">
                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="24" cy="24" r="24" fill="#FFF176"/>
                                    <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"/>
                                </svg>
                                @if( $iqc){{$iqc->amountofIQC}} @else 0 @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <p>{!! __('message.history')!!}</p>
                            </div>
                        </div>
                        <div class="row">
                            <style>
                                .btn-def1, .btn-def2{
                                    background: #4B96DC;
                                    border: 1px solid #E6F0F0;
                                    border-radius: 6px;
                                    display: flex;
                                    flex-direction: row;
                                    justify-content: center;
                                    align-items: center;
                                    padding: 12px 16px;
                                    gap: 10px;
                                    font-weight: 400;
                                    font-size: 12px;
                                    color: #E6F0F0;
                                }
                                .btn-def2{
                                    background: #4DB1B1;
                                }
                                .table-history{
                                    max-height: 0;
                                    overflow: hidden;
                                    transition: all 0.3s ease;
                                }
                                .table-history.active{
                                    max-height: 10000px;
                                }
                                .table-history thead th{
                                    font-weight: 600;
                                    font-size: 14px;
                                    border-top: 0;
                                    line-height: 17px;
                                    border-bottom: 1px solid rgba(0, 0, 0, 0.4);
                                }
                                .table-history  tbody td{
                                    font-weight: 400;
                                    font-size: 14px;
                                    line-height: 17px;
                                    border-bottom: 1px solid rgba(0, 0, 0, 0.4);
                                    
                                }
                                .table-history  tbody td span{
                                    font-weight: 400;
                                }
                                .theme-dark .table-history thead th, .theme-dark .table-history  tbody td{
                                    color: white;
                                }
                            </style>
                            <div class="col-md-6">
                                <div onclick="document.querySelector('.table-history').classList.toggle('active'); window.location.href='#history2'" class="btn btn-def1 form-control">{{ __('message.history2')}} <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 0C7.03 0 3 4.03 3 9H0L3.89 12.89L3.96 13.03L8 9H5C5 5.13 8.13 2 12 2C15.87 2 19 5.13 19 9C19 12.87 15.87 16 12 16C10.07 16 8.32 15.21 7.06 13.94L5.64 15.36C7.27 16.99 9.51 18 12 18C16.97 18 21 13.97 21 9C21 4.03 16.97 0 12 0ZM11 5V10L15.28 12.54L16 11.33L12.5 9.25V5H11Z" fill="#E6F0F0"/>
                                    </svg>
                                </div>
                                <div class="table-history mt-2" >
                                    <table class="table">
                                        
                                        <thead>
                                          <tr>
                                            <th scope="col">{{ __('message.get')}}</th>
                                            <th scope="col">{{ __('message.from')}}</th>
                                            <th scope="col">{{ __('message.when')}}</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><svg style="margin-right: 10px" width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="24" cy="24" r="24" fill="#FFF176"></circle>
                                                <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"></circle>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"></path>
                                            </svg>+5</td>
                                            <td>Реферальной ссылки</td>
                                            <td>01/07/2022 20:30</td>
                                          </tr>
                                          <tr>
                                            <td><svg style="margin-right: 10px" width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="24" cy="24" r="24" fill="#FFF176"></circle>
                                                <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"></circle>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"></path>
                                            </svg>+20</td>
                                            <td>За прохождения теста</td>
                                            <td>01/07/2022 20:30</td>
                                          </tr>
                                          <tr>
                                            <td><svg style="margin-right: 10px" width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="24" cy="24" r="24" fill="#FFF176"></circle>
                                                <circle cx="24" cy="24" r="22.5" fill="#F2BC1A"></circle>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9892 11.9239L11.1531 27.1116C10.8692 27.5997 10.8121 28.1597 10.9497 28.6681C11.0867 29.1766 11.4179 29.6332 11.91 29.9151C12.2386 30.1034 12.5997 30.1909 12.956 30.1876V30.1866H13.0064C13.1662 30.1812 13.3246 30.1577 13.4783 30.1169C13.9906 29.9806 14.4509 29.6519 14.7347 29.1637L21.6596 17.2609L23.5708 13.9762L23.5723 13.977C23.8672 13.4621 24.2988 13.016 24.8508 12.6997C25.6797 12.2249 26.628 12.1296 27.4883 12.3583C28.349 12.587 29.1221 13.1402 29.6008 13.9628L38.4366 29.1505C38.6313 29.4849 38.5159 29.9127 38.179 30.1057C37.842 30.2988 37.4107 30.1841 37.2164 29.8498L28.3803 14.662C28.0964 14.1739 27.6359 13.8452 27.1238 13.709C26.6115 13.5727 26.0472 13.6291 25.5555 13.9109C25.0637 14.1927 24.7325 14.6493 24.5953 15.1577C24.4578 15.6663 24.5147 16.226 24.7986 16.7141L31.7235 28.617L33.6347 31.9019L33.6322 31.9033C33.9329 32.4134 34.1055 33.0061 34.1055 33.6372C34.1055 34.5869 33.7144 35.4497 33.0844 36.0747C32.4545 36.6998 31.5852 37.0877 30.628 37.0877H12.956C12.5671 37.0877 12.2513 36.7748 12.2513 36.3884C12.2513 36.0024 12.5671 35.6892 12.956 35.6892H30.628C31.1962 35.6892 31.7131 35.4583 32.0882 35.086C32.4633 34.7138 32.6959 34.2008 32.6963 33.6372C32.6963 33.0735 32.4633 32.5606 32.0882 32.1885C31.7131 31.816 31.1962 31.585 30.628 31.585H16.7781H13.0306C12.4107 31.6019 11.7792 31.455 11.2052 31.1261C10.3765 30.6513 9.81899 29.8838 9.5885 29.0299C9.35783 28.1761 9.45403 27.2349 9.93259 26.4124L18.7687 11.2246C18.963 10.8902 19.3942 10.7757 19.7312 10.9689C20.0682 11.1618 20.1836 11.5895 19.9892 11.9239ZM30.0665 30.1866C30.1261 30.1727 30.1844 30.1504 30.2396 30.1185C30.5423 29.9452 30.6457 29.5618 30.4713 29.2617L30.0328 28.5083L23.867 17.9103C23.8233 17.862 23.7716 17.8195 23.7121 17.7853C23.4096 17.6121 23.023 17.7149 22.8484 18.0148L22.4102 18.7683L16.2354 29.3817C16.2188 29.4379 16.21 29.4976 16.21 29.5593C16.21 29.9055 16.4928 30.1864 16.8417 30.1866H17.7185H30.0665ZM20.4624 13.8161C20.5596 13.6489 20.7161 13.5369 20.8901 13.4905C21.0643 13.4441 21.2565 13.4638 21.4249 13.5604C21.5934 13.6567 21.7065 13.812 21.7531 13.9849L21.7527 13.9851C21.7992 14.1583 21.7797 14.3488 21.6827 14.5154L13.8791 27.9289C13.7821 28.0953 13.626 28.2073 13.4516 28.2539L13.4507 28.2541C13.2763 28.3004 13.0842 28.2809 12.9165 28.1846C12.7488 28.0884 12.6356 27.9332 12.5886 27.76L12.5885 27.7601C12.5415 27.5873 12.5613 27.3966 12.6587 27.2297L20.4624 13.8161ZM14.3708 34.3364C14.1763 34.3364 14.0001 34.258 13.8725 34.1315C13.7452 34.0051 13.6661 33.8303 13.6661 33.6372C13.6661 33.4442 13.7452 33.2692 13.8725 33.1428L13.8731 33.1431C14.0007 33.0163 14.1771 32.938 14.3708 32.938H29.9782C30.172 32.938 30.3477 33.0161 30.4756 33.1428L30.4765 33.1434C30.6038 33.2702 30.683 33.445 30.683 33.6372C30.683 33.8295 30.6038 34.0044 30.4762 34.1312L30.4765 34.1315C30.3489 34.258 30.1728 34.3364 29.9782 34.3364H14.3708ZM35.3984 29.8409C35.4454 29.668 35.4257 29.4775 35.3282 29.3104L27.5245 15.8971C27.4273 15.7299 27.271 15.6176 27.0968 15.5713V15.5718C26.922 15.5254 26.7301 15.545 26.5619 15.6411C26.3944 15.7373 26.2813 15.8924 26.2344 16.0654L26.234 16.0664C26.1877 16.2394 26.2075 16.4297 26.3042 16.5963L34.1078 30.0096C34.2048 30.1762 34.3613 30.2885 34.536 30.3349L34.5359 30.3353C34.71 30.3816 34.9019 30.3621 35.0704 30.2656C35.2389 30.169 35.3521 30.0138 35.3984 29.8409Z" fill="#FFF176"></path>
                                            </svg><span class="text-danger">-200</span></td>
                                            <td>Покупка курса</td>
                                            <td>01/07/2022 20:30</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                      <div id="history2"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="https://store.pharmiq.uz/" class="btn btn-def2 form-control">{{ __('message.magazine')}} <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 0H1V2H17V0ZM18 10V8L17 3H1L0 8V10H1V16H11V10H15V16H17V10H18ZM9 14H3V10H9V14Z" fill="#E6F0F0"/>
                                    </svg></a>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
            @if(auth()->user()?->role=='Company Owner')
            <div class="tab-pane fade" id="navtabs6">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block">{{ __('message.mypharmacy')}} <div onclick="startedit('apteka','companyform)" class="float-right changedui">Изменить<i class="feather-edit text-grey-500 font-xs"></i></div></h2></div>
                    <div class="card-body pb-0">
                       <div class="row">
                        <div class="col-md-12">
                            <div class="hero__form--body">
                               
                                <form action="{{route('profile.profileCompany')}}" class="companyform"  method="POST">
                                 @csrf
                                 
                                 <div class="form__wrap icon">
                                    <input type="text" class="form-control" name="companyName" onkeydown="return false;" value="{{$company?->companyName}}" required="">
                                    <label>{{ __('message.companyname')}}</label>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 2C9.1 2 10 2.9 10 4C10 5.1 9.1 6 8 6C6.9 6 6 5.1 6 4C6 2.9 6.9 2 8 2ZM8 12C10.7 12 13.8 13.29 14 14H2C2.23 13.28 5.31 12 8 12ZM8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0ZM8 10C5.33 10 0 11.34 0 14V16H16V14C16 11.34 10.67 10 8 10Z" fill="#A1A1A1"></path>
                                      </svg>
                                  </div>
                                  <p>{{ __('message.writecompanyname')}}</p>
                                  <div class="form__wrap">
                                    <select name="city_id" onchange="getRegionID(this.value)" class="form-control" id="">
                                        @forelse ($cities  as $city)
                                        <option value="{{$city->id}}" @if($company->companyadress?->city_id==$city->id) selected @endif>{{$city->name_ru}}</option>
                                        @empty
                                            
                                        @endforelse
                                        
                                      </select>
                                  </div>
                                  <div class="form__wrap">
                                   
                                    <select name="region_id"  class="form-control" id="regionid">
                                        @forelse ($regions  as $region)
                                            <option value="{{$region->id}}" @if($company->companyadress?->region_id==$region->id) selected @endif>{{$region->name_ru}}</option>
                                        @empty
                                            
                                        @endforelse
                                        
                                      </select>
                                      <script>
                                        function getRegionID(city_id){
                                        
                                        fetch("{{route('profile.profileGetRegion')}}/?city_id="+city_id)
                                            .then((resp) => resp.json()).then(function(data){
                                                let options = '';
                                                data.regions.forEach(region => {
                                                    options+='<option value="'+region.id+'">'+region.name_ru+'</option>'
                                                        
                                                    });    
                                                    document.querySelector('#regionid').innerHTML = options;
                                                })
                                                    .catch(function(error) {
                                                        console.log(error);
                                                    });
                                        }
                                      </script>
                                  </div>
                                  <div class="form__wrap iconno rightone">
                                    <input type="text" class="form-control" name="street" onkeydown="return false;" value="{{isset(explode(",",$company?->companyadress?->addressline1)[0])? explode(",",$company?->companyadress?->addressline1)[0] : ''}}" required="">
                                    <label>{{ __('message.street')}}</label>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 2C9.1 2 10 2.9 10 4C10 5.1 9.1 6 8 6C6.9 6 6 5.1 6 4C6 2.9 6.9 2 8 2ZM8 12C10.7 12 13.8 13.29 14 14H2C2.23 13.28 5.31 12 8 12ZM8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0ZM8 10C5.33 10 0 11.34 0 14V16H16V14C16 11.34 10.67 10 8 10Z" fill="#A1A1A1"></path>
                                      </svg>
                                  </div>
                                  <div class="form__wrap iconno rightone">
                                    <input type="text" class="form-control" name="house" onkeydown="return false;" value="{{isset(explode(",",$company?->companyadress?->addressline1)[1])? explode(",",$company?->companyadress?->addressline1)[1] : ''}}" required="">
                                    <label>{{ __('message.house')}}</label>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 2C9.1 2 10 2.9 10 4C10 5.1 9.1 6 8 6C6.9 6 6 5.1 6 4C6 2.9 6.9 2 8 2ZM8 12C10.7 12 13.8 13.29 14 14H2C2.23 13.28 5.31 12 8 12ZM8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0ZM8 10C5.33 10 0 11.34 0 14V16H16V14C16 11.34 10.67 10 8 10Z" fill="#A1A1A1"></path>
                                      </svg>
                                  </div>
                                  <input type="hidden" name="platform" value="website">
                                  <input type="hidden" name="device" value="tablet">
                                  <input type="hidden" name="browser" value="chrome">
                                  <input type="hidden" name="timeZone" value="500">

                                  <button id="apteka" class="btn btn-secondary" style="display: none">
                                    <span>{{ __('message.save')}} <svg class="ml-1" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.6667 0H2.66667C1.18667 0 0 1.2 0 2.66667V21.3333C0 22.8 1.18667 24 2.66667 24H21.3333C22.8 24 24 22.8 24 21.3333V5.33333L18.6667 0ZM12 21.3333C9.78667 21.3333 8 19.5467 8 17.3333C8 15.12 9.78667 13.3333 12 13.3333C14.2133 13.3333 16 15.12 16 17.3333C16 19.5467 14.2133 21.3333 12 21.3333ZM16 8H2.66667V2.66667H16V8Z" fill="white"/>
                                        </svg></span> 
                                  </button>
                                </form>
                              </div>
                        </div>
                       </div>
                    </div>  
                </div>
            </div>
            
            <div class="tab-pane fade" id="navtabs7">
                <div class="card d-block w-100 border-0 shadow-xss rounded-lg overflow-hidden p-4">
                    <div class="card-body mb-3 pb-0"><h2 class="fw-400 font-lg d-block">{{ __('message.myteam')}}</h2></div>
                    <div class="card-body pb-0">
                        
                       <div class="row">
                        <div class="col-md-12">
                            <div class="hero__form--body" style="width: 100%;">
                               
                                
                                
                                 <style>
                                    .borderless{
                                        width: 90%
                                    }
                                    .borderless td, .borderless th {
                                            border: none!important;
                                    }
                                    .borderless tr{
                                        display: flex;
                                        width: 100%; 
                                        align-items: center;
                                    }
                                    .borderless tr td, .borderless tr th{
                                       width: 150px;
                                    }
                                    .borderless tr td:last-child{
                                        flex: 2;
                                    }
                                    .borderless tr td:first-child{
                                        flex: 2;
                                    }
                                    .borderless .radio__button.man{
                                        background-color: #FF038E!important;
                                    }
                                    .borderless .woman{
                                        background-color: #45A9D3!important;
                                    }
                                    @media (max-width: 600px) {
                                        .borderless .form__wrap--radio_each .radio__text {
                                           display: none!important;
                                        }
                                        .borderless .form__wrap--radio_each.active .radio__text{
                                            display: block!important;
                                        }
                                        .hero__form--body .borderless .form__wrap--radio{
                                            height: 27px;
                                        }
                                        .hero__form--body .borderless .form__wrap--radio .radio__button{
                                            width: 83%;
                                        }
                                        .hero__form--body .borderless .form__wrap--radio .radio__button.woman{
                                            left: 24px;
                                        }
                                        .borderless tbody{
                                            position: relative;
                                            left: -52px;
                                        }
                                        .borderless .form__wrap--radio_each.active:nth-child(2) .radio__text span{
                                            position: relative;
                                            top: 12px;
                                            right: -13px;
                                        } 
                                        .borderless .form__wrap--radio_each.active:nth-child(1) .radio__text span{
                                            position: relative;
                                            top: 12px;
                                            left: -9px;
                                        } 
                                    }
                                </style>
                                <table class="table borderless" >
                                   
                                    <tbody>
                                        
                                        @forelse ($company->companymembers as $k=>$member)
                                        @php
                                            
                                            $user = \App\Models\User::find($member->member_id);
                                           
                                        @endphp
                                        @if($user?->phonebook)
                                        <tr>
                                            <th>{{$user?->phonebook?->phoneNumber}}</th>
                                            <td>{{$user?->firstName}}</td>
                                            <td>{{$user?->lastName}}</td>
                                            <td><div class="moving{{$k}}">
                                                <div class="form__wrap--radio" style="position: relative">
                                                    <div class="form__wrap--radio_each {{!$member->memberStatus? 'active' : ''}}" onclick="chooseGender{{$k}}(this,0, {{$user->id}})">
                                                        <div class="radio__text">
                                                            
                                                            <span>{{ __('message.pending')}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="form__wrap--radio_each {{$member->memberStatus? 'active' : ''}}" onclick="chooseGender{{$k}}(this,1,{{$user->id}} )">
                                                        <div class="radio__text">
                                                            
                                                            <span>{{ __('message.active')}}</span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="gender{{$k}}" name="gender2">
                                                    <div class="radio__button {{!$member->memberStatus? 'man' : 'woman'}}  "></div></div>
                                                <script>
                                                  function chooseGender{{$k}}(e, gender, user_id){
                                                    document.querySelector('#gender{{$k}}').value = gender;
                                                    setfetch(user_id, gender)
                                                    document.querySelectorAll('.moving{{$k}} .form__wrap--radio_each').forEach(element => {
                                                      element.classList.remove('active');
                                                    });
                                                    e.classList.add('active');
                                                    let radiobtn = document.querySelector('.moving{{$k}} .radio__button');
                                                    radiobtn.classList.remove('man', 'woman');
                                                    if(gender){
                                                      radiobtn.classList.add('woman');
                                                    }else{
                                                      radiobtn.classList.add('man');
                                                    }
                                                  }
                                                </script>
                                                </div>
                                            </td>
                                          </tr>
                                          @endif
                                        @empty
                                            
                                        @endforelse
                                    
                                   
                                    </tbody>
                                  </table>
                                  
                                  <input type="hidden" name="platform" value="website">
                                  <input type="hidden" name="device" value="tablet">
                                  <input type="hidden" name="browser" value="chrome">
                                  <input type="hidden" name="timeZone" value="500">

                                  <script>
                                    function setfetch(user_id, status){
                                        
                                        fetch("{{route('profile.profileCompanyMembersApprove')}}/?user_id="+user_id+"&memberStatus="+status)
                                        .then((resp) => resp.json()).then(function(data){
                                                console.log($data);
                                            })
                                                .catch(function(error) {
                                                    console.log(error);
                                                });
                                    }
                                    
                                  </script>
                              </div>
                        </div>
                       </div>
                    </div>  
                </div>
            </div>
            @endif

             
        </div>
    </div>
    <script>
        function startedit(id, classsf){
            console.log(classsf);
            document.querySelector('#'+id).style.display="block";
            document.querySelectorAll('form.'+classsf+' input').forEach(element => {
                    element.removeAttribute("onkeydown");
                });
        }
    </script>
    @include('layouts.right')
</div>
@endsection