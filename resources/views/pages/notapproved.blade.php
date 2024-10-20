@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left" id="newonep">
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
        max-width: 480px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        
    }
    
    .result.red{
        background: #FF736E;
    }
    .result__title{
        font-weight: 600;
        font-size: 32px;
        line-height: 39px;
        text-align: center;
        color: #E6F0F0;
        margin-bottom: 20px;
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
            align-items: center;
            justify-content: center;
        }
    }
</style>
<div class="row" style="min-height: 80vh">
    <div class="col-md-12" >
        <div class="result red">
            <div class="result__title">{{$language? 'Платформа станет доступной, как только Заведующий подтвердить, что Вы часть этой аптеки' : "Platforma, sizni dorixona yurg;zuvchisi tasdiqlagandan so'ng, dar hol sizga majud bo'ladi"}}</div>
            
            <div class="result__buttons">
                <div class="result__buttons--secondary btn" onclick="location.reload();">{{$language? 'Проверить' : 'Tekshirish'}} <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M23 12L20.56 9.21L20.9 5.52L17.29 4.7L15.4 1.5L12 2.96L8.6 1.5L6.71 4.69L3.1 5.5L3.44 9.2L1 12L3.44 14.79L3.1 18.49L6.71 19.31L8.6 22.5L12 21.03L15.4 22.49L17.29 19.3L20.9 18.48L20.56 14.79L23 12ZM10.09 16.72L6.29 12.91L7.77 11.43L10.09 13.76L15.94 7.89L17.42 9.37L10.09 16.72Z" fill="white"/>
</svg>
                    </div>
                
            </div>
        </div>
    </div>
</div>
    </div>
   
</div>  
@endsection