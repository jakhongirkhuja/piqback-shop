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
            <div class="result__title">{{ __('message.badresponse')}}</div>
            
            <div class="result__buttons">
                <div class="result__buttons--secondary btn" onclick="location.reload();">{{ __('message.repeattest')}} <svg class="ml-2" width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.17695 19.5669C2.03095 17.0209 0.257949 11.7579 2.21695 7.14388C4.37495 2.05988 10.2449 -0.312121 15.3289 1.84588C20.4129 4.00388 22.7849 9.87488 20.6269 14.9589C19.8189 16.8703 18.4337 18.4814 16.6649 19.5669" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16.5 15V19.4C16.5 19.5591 16.5632 19.7117 16.6757 19.8243C16.7883 19.9368 16.9409 20 17.1 20H21.5M11.5 21.01L11.51 20.999" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </div>
                <a href="{{route('lesson.web',['id'=>$lesson])}}" class="result__buttons--primary btn mt-2" >{{ __('message.repeatlesson')}} <svg class="ml-2" width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 4V0L0 7L7 14V9.9C12 9.9 15.5 11.5 18 15C17 10 14 5 7 4Z" fill="#E6F0F0"/>
                    </svg></a> 
            </div>
        </div>
    </div>
</div>