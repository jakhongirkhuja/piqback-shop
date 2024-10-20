@extends('layouts.app')
@section('main')
<div class="middle-sidebar-bottom">
    <div class="middle-sidebar-left" id="newonep">
        <div class="row" id="newone">
            <div class="col-xl-12 col-xxl-12">
                
                <div class="card d-block border-0 rounded-lg overflow-hidden dark-bg-transparent bg-transparent mt-4 pb-3">
                    <div class="row">
                        <div class="col-xl-9">
                            <h5>{{$language? json_decode($lesson->lessonTitleName)->ru : json_decode($lesson->lessonTitleName)->uz}}</h5>
                            @if ($lesson->quizes)
                                @forelse ($lesson->quizes->questions as $question)
                                <h1 id="question">{{$language? json_decode($question->question)->ru : json_decode($question->question)->uz}}</h1>
                                <p id="questionTextOne">@if(json_decode($question->questionTextOne)->ru!='' || json_decode($question->questionTextOne)->uz!='')
                                {{$language? json_decode($question->questionTextOne)->ru : json_decode($question->questionTextOne)->uz}}
                                @endif</p>
                                <p id="questionTextTwo">@if(json_decode($question->questionTextTwo)->ru!='' || json_decode($question->questionTextTwo)->uz!='')
                                {{$language? json_decode($question->questionTextTwo)->ru : json_decode($question->questionTextTwo)->uz}}
                                @endif</p>
                                @php  break; @endphp
                                @empty
                                
                                @endforelse
                            @endif
                            
                        </div>
                        
                        <div class="col-xl-3">
                            <style>
                                .btn-outline-info{
                                    border: 0px;
                                    background: #FFFFFF;
                                    box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.25);
                                    border-radius: 10px;
                                    pointer-events: none;
                                    font-size: 24px;
                                    padding: 11px 15px;
                                }
                                .cust{
                                    background: #4DB1B1;
                                    box-shadow: 2px 2px 4px rgba(0, 205, 190, 0.15);
                                    border-radius: 8px;
                                    font-size: 30px;
                                    border: none;
                                    pointer-events: none;
                                    margin-left: 20px;
                                    width: 125px;
                                }
                                .red{
                                    background-color: #FF736E;
                                }
                                .yellow{
                                    background-color: #F2C94C;
                                }
                                .contain label{
                                    background-color: #E6F0F0;
                                    color: #000000;
                                    border: none;
                                    padding: 16px 24px;
                                }
                                .contain input.btn-check{
                                    display: none;
                                }
                                .contain input.btn-check:checked ~ label,  .contain label:hover {
                                background-color: #007382;
                                color: #E6F0F0;
                                }
                                .cust2, .cust2:hover{
                                    background: #007382;
                                    border-radius: 5px;
                                    padding: 7px 20px;
                                    font-size: 16px;
                                    border: none;
                                }
                                .cust2:hover{
                                    opacity: 0.9;
                                }
                                .cust2:disabled{
                                    background-color: #999999;
                                }
                                .cust.mi{
                                    animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) infinite;
                                    transform: translate3d(0, 0, 0);
                                    perspective: 1000px;
                                }
                                @keyframes shake {
                                    10%, 90% {
                                        transform: translate3d(-1px, 0, 0);
                                    }
                                    20%, 80% {
                                        transform: translate3d(2px, 0, 0);
                                    }
                                    30%, 50%, 70% {
                                        transform: translate3d(-4px, 0, 0);
                                    }
                                    40%, 60% {
                                        transform: translate3d(4px, 0, 0);
                                    }
                                    }
                            </style>
                            <button class="btn btn-outline-info" ><span id="quiz_count">4</span>/{{$lesson->quizes? $lesson->quizes->questions->count() : 0}}</button>
                            <button class="btn btn-secondary cust" >00:00</button>  <!-- color changing -->
                            
                        </div>
                    </div>
                    
                    
                </div>
                
                <div  class="card d-block border-0 text-left rounded-lg overflow-hidden p-4 shadow-xss mt-4">
                    <div id="checkbuttons">
                        @if($lesson->quizes)
                        @forelse ($lesson->quizes->questions[0]->variants as $k=>$variant)
                            <div class="contain">
                                <input type="radio" class="btn-check" name="ok" data-id="{{$variant->id}}" value="{{$variant->id}}" id="btn-check-{{$k}}"  autocomplete="off">
                                <label class="btn btn-primary w-100 text-left" onclick="buttonclicked()"  for="btn-check-{{$k}}">{{$language? json_decode($variant->variantText)->ru : json_decode($variant->variantText)->uz}}</label>
                            </div>
                            @empty
                                
                            @endforelse
                        @endif
                        
                    </div>
                    
                    
                    <div class="nextbtn text-right mt-2">
                        <button class="btn-primary btn cust2"  data-id="0" disabled onclick="submitVariant(this)">{{ __('message.nextbtn')}}</button>
                    </div>
                </div>

                

            </div>
          

        </div>
    </div>
    @include('layouts.right')
</div>  
<script>
    let quiz = {}; 
    let pushtoVar = [];
    let eachvar = {
        'variantText_ru': '',
        'variantText_uz': '',
        'id':'',
        'choose': false,
    } 
    let variants = {
        'question_ru' : '',
        'question_uz' : '',
        'questionTextOne_ru' : '',
        'questionTextOne_uz' : '',
        'questionTextTwo_ru' : '',
        'questionTextTwo_uz' : '',
        'variants': []

    }
    let question = []
    let ok ;
    @if ($lesson->quizes)
        quiz.timeLimits = {{$lesson->quizes->timeLimits}};
        quiz.id = {{$lesson->quizes->id}};
        quiz.numberRightAnswersToPass = {{$lesson->quizes->numberRightAnswersToPass}};
        quiz.language = {{$language? 1 : 0}};
        @forelse($lesson->quizes->questions as $question)
            @if(isset(json_decode($question->question)->ru))
                variants.question_ru = '{{json_decode($question->question)->ru}}';
            @endif
            @if(isset(json_decode($question->question)->uz))
                variants.question_uz = '{{json_decode($question->question)->uz}}';
            @endif
            @if(isset(json_decode($question->questionTextOne)->ru))
                variants.questionTextOne_ru = '{{json_decode($question->questionTextOne)->ru}}';
            @endif
            @if(isset(json_decode($question->questionTextOne)->uz))
                variants.questionTextOne_uz = '{{json_decode($question->questionTextOne)->uz}}';
            @endif
            @if(isset(json_decode($question->questionTextTwo)->ru))
                variants.questionTextTwo_ru = '{{json_decode($question->questionTextTwo)->ru}}';
            @endif
            @if(isset(json_decode($question->questionTextTwo)->uz))
                variants.questionTextTwo_ru = '{{json_decode($question->questionTextTwo)->uz}}';
            @endif
            @forelse($question->variants()->inRandomOrder()->get() as $variant)
               
                @if(isset(json_decode($variant->variantText)->ru))
                    eachvar.variantText_ru='{{json_decode($variant->variantText)->ru}}';
                @endif
                @if(isset(json_decode($variant->variantText)->uz))
                    eachvar.variantText_uz='{{json_decode($variant->variantText)->uz}}';
                @endif
                eachvar.id ={{$variant->id}};
               
                pushtoVar.push(eachvar);
                eachvar = {
                        'variantText_ru': '',
                        'variantText_uz': '',
                        'id':'',
                        'choose': false,
                    } 
            @empty 
            @endforelse
            variants.variants = pushtoVar;
            pushtoVar =[];
            question.push(variants);
            variants = {
                    'question_ru' : '',
                    'question_uz' : '',
                    'questionTextOne_ru' : '',
                    'questionTextOne_uz' : '',
                    'questionTextTwo_ru' : '',
                    'questionTextTwo_uz' : '',
                    'variants': []

                }
        @empty
        @endforelse
    @endif
   
    changeUI(0);
    function changeUI(id){
        let ques = question[id];
        let string = '';
        let counter = 0;
        if(ques){
            if(quiz.language){
                document.querySelector('#question').innerHTML  = ques.question_ru;
                document.querySelector('#questionTextOne').innerHTML  = ques.questionTextOne_ru;
                document.querySelector('#questionTextTwo').innerHTML  = ques.questionTextTwo_ru;
            }else{
                document.querySelector('#question').innerHTML  = ques.question_uz;
                document.querySelector('#questionTextOne').innerHTML  = ques.questionTextOne_uz;
                document.querySelector('#questionTextTwo').innerHTML  = ques.questionTextTwo_uz;
            }
            document.querySelector('#quiz_count').innerHTML = parseInt(id)+1;
            ques.variants.forEach(variant => {
                if(quiz.language){
                    string+='<div class="contain"><input type="radio" class="btn-check" name="ok"  value="'+variant.id+'" id="btn-check-'+counter+'"  autocomplete="off"><label class="btn btn-primary w-100 text-left" onclick="buttonclicked()"  for="btn-check-'+counter+'">'+variant.variantText_ru+'</label></div>';
                }else{
                    string+='<div class="contain"><input type="radio" class="btn-check" name="ok"  value="'+variant.id+'" id="btn-check-'+counter+'"  autocomplete="off"><label class="btn btn-primary w-100 text-left" onclick="buttonclicked()"  for="btn-check-'+counter+'">'+variant.variantText_uz+'</label></div>';
                }
                counter ++ ;
            });
            document.querySelector('#checkbuttons').innerHTML = string;
        }
        console.log(question[id]);
    }
    function buttonclicked(){
        document.querySelector('.cust2').removeAttribute('disabled');
    }
    function submitVariant(e){
        document.querySelector('.cust2').setAttribute('disabled','');
        let getvalue = document.querySelector('input[name="ok"]:checked').value;
        let getindex = parseInt(e.dataset.id);
        
        if(question[getindex] && question[getindex].variants){
            question[getindex].variants.forEach(elem => {
                if(elem.id==getvalue){
                    elem.choose = true;
                }
            });
        }
        e.dataset.id = getindex +1;
       
        if(e.dataset.id < question.length  ){
            changeUI(e.dataset.id)
        }else{
            window.localStorage.clear();
            window.onbeforeunload = null;
            clearTest()
           
        }
        // console.log(question[getindex]);
    }
    let lasttime="00:00:00";
    function clearTest(){
        document.querySelector('#newone').remove();
        let formData = new FormData();
        formData.append('quiz_id', quiz.id);
        formData.append('question', JSON.stringify(question));
        formData.append('lesson_id', {{$lesson->id}});
        formData.append('timeLeft',lasttime);

        
        fetch("{{route('lessonPost.quiz.web')}}",
            {
               
                body: formData,
                method: "post"
            }).then(response => 
                response.json().then(data => ({
                    data: data,
                    status: response.status
                })
            ).then(res => {
               
                    document.querySelector('#newonep').innerHTML=res.data.view;
               
            }));
    }
    
    function startTimer(duration, display) {
         var timer = duration, minutes, seconds;
         let fiveminutss =  true;
         let threeminuts = true;
         let lasttensecond = true;
            setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                console.log(minutes);
                if(fiveminutss && minutes<5){
                    console.log('inside');
                    fiveminutss = false;
                    document.querySelector('.cust').classList.add('yellow');
                }
                if(threeminuts && minutes<3){
                    threeminuts = false;
                    document.querySelector('.cust').classList.remove('yellow');
                    document.querySelector('.cust').classList.add('red');
                }
                if(lasttensecond && minutes<=0 && seconds<=10){
                    document.querySelector('.cust').classList.add('mi');
                }
                if(minutes==0 && seconds==0){
                    clearTest();
                    //alert('end');
                }
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;
                lasttime = "00:"+minutes + ":" + seconds;
                if (--timer < 0) {
                    timer = duration;
                }
            }, 1000);
        }
        var message = "You have not filled out the form.";
                window.onbeforeunload = function(event) {
                    var e = e || window.event;
                    if (e) {
                        e.returnValue = message;
                    
                    }
                    
                    return message;
                };
        window.onload = function() {
            let totalMinut = quiz.timeLimits;
            
            quiz.id
            let get60percent = 60 * totalMinut/ 100;
            let get20percent = 20 * totalMinut/ 100;
            // window.localStorage.clear();
            if(localStorage.getItem('quiz_id')== quiz.id){
                if(localStorage.getItem('minute')){
                    window.localStorage.clear();
                    window.onbeforeunload = null;
                    clearTest();
                }
            }
            var minute = localStorage.getItem('minute')? localStorage.getItem('minute') : quiz.timeLimits-1;
            // var minute = 0;
             localStorage.setItem('quiz_id', quiz.id);
            var sec =localStorage.getItem('sec')? localStorage.getItem('sec') :  59;
            
                    setInterval(function() {
                        if(document.querySelector(".cust")){
                           
                        localStorage.setItem('minute', minute);
                        localStorage.setItem('sec', sec);
                        let choosenMinute = minute>=10 ? minute : '0'+minute;
                        let choosenSec = sec >=10 ? sec : '0'+sec;
                        

                    
                        document.querySelector(".cust").innerHTML = choosenMinute + " : " + choosenSec;
                        sec--;
                        if(get60percent >=minute){
                            document.querySelector('.cust').classList.add('yellow');
                        }
                        if(get20percent >=minute){
                            document.querySelector('.cust').classList.remove('yellow');
                            document.querySelector('.cust').classList.add('red');
                        }
                        if(minute==0 && sec<10){
                            document.querySelector('.cust').classList.add('mi');
                        }
                       
                        if (sec == 00) {
                            
                           
                            if (minute == 0 && (sec==0 || sec==00)) {
                                
                                lasttime = "00:"+choosenMinute + ":" + choosenSec;
                                window.localStorage.clear();
                                window.onbeforeunload = null;
                                clearTest();
                            
                            }
                            minute --;
                            sec = 59;
                        }
                    }
                    }, 1000);
                
            }
</script>
@endsection