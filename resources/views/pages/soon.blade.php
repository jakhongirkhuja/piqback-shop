@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left">
        <style>
            .result{
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%,-50%);
                background: #4DB1B1;
                box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.25);
                border-radius: 4px;
                padding: 30px 50px;
                width: 480px;
                min-height: 430px;
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }
            .result.red{
                background: #FF736E;
            }
            .result__title{
                font-weight: 600;
                font-size: 16px;
                line-height: 20px;
                text-align: center;
                color: #E6F0F0;
                margin-bottom: 20px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
            }
            .result__title svg{
                margin-bottom: 21px;
            }
            .result__coins{
                display: flex;
                justify-content: center;
            }
            .result__coins span{
                font-weight: 600;
                font-size: 64px;
                line-height: 77px;
                color: #007382;
            }
            .result__coins svg{
                margin-left: 18px;
            }
            .result__buttons{
                padding-top: 40px;
            }
            .result__buttons .result__buttons--primary, .result__buttons .result__buttons--secondary{
                background: #4B96DC;
                border-radius: 5px;
                font-weight: 600;
                font-size: 15px;
                line-height: 20px;
                color: #FFFFFF;
                padding: 14px 24px;
                
                align-items: center;
            } 
            .result__buttons .result__buttons--secondary{
                background: #4DB1B1;
            }
            @media only screen and (max-width: 576px) {
                .result{
                    width: 90%;
                    padding: 30px;
                }
                .result__title{
                    font-size: 23px;
                    line-height: 30px;
                }
                .result__buttons .result__buttons--primary, .result__buttons .result__buttons--secondary{
                    display: flex;
                    justify-content: center;
                }
            }
        </style>
        <div class="row" style="min-height: 80vh">
            <div class="col-md-12" >
                <div class="result red pt-5">
                    <div class="result__title">
                        <svg width="44" height="57" viewBox="0 0 44 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M37.9998 19.3332H35.3332V13.9998C35.3332 6.63984 29.3598 0.666504 21.9998 0.666504C14.6398 0.666504 8.6665 6.63984 8.6665 13.9998V19.3332H5.99984C3.0665 19.3332 0.666504 21.7332 0.666504 24.6665V51.3332C0.666504 54.2665 3.0665 56.6665 5.99984 56.6665H37.9998C40.9332 56.6665 43.3332 54.2665 43.3332 51.3332V24.6665C43.3332 21.7332 40.9332 19.3332 37.9998 19.3332ZM21.9998 43.3332C19.0665 43.3332 16.6665 40.9332 16.6665 37.9998C16.6665 35.0665 19.0665 32.6665 21.9998 32.6665C24.9332 32.6665 27.3332 35.0665 27.3332 37.9998C27.3332 40.9332 24.9332 43.3332 21.9998 43.3332ZM30.2665 19.3332H13.7332V13.9998C13.7332 9.43984 17.4398 5.73317 21.9998 5.73317C26.5598 5.73317 30.2665 9.43984 30.2665 13.9998V19.3332Z" fill="white"/>
                            </svg>
                        <p>{{__('message.appearsoon')}}
                        </p>
                    </div>
                    
                    <div class="result__buttons mb-4">
                       
                        <a href="{{route('index')}}" class="result__buttons--primary btn mt-2">{{__('message.mainMenu')}} <svg class="ml-2" width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 4V0L0 7L7 14V9.9C12 9.9 15.5 11.5 18 15C17 10 14 5 7 4Z" fill="#E6F0F0"/>
                            </svg></a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.right')
</div>  
@endsection