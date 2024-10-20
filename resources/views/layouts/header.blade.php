
@php 
$userinfo = \App\Models\User::find(auth()->user()? auth()->user()->id : 1);
$iqcinfo =  \App\Models\Money\Iqc::where('user_id', auth()->user()? auth()->user()->id : 1)->first();
@endphp
<div class="middle-sidebar-header bg-white">
    <button class="header-menu"></button>
    <style>
         .av__girl{
            background: linear-gradient(107.9deg, #FF736E 0%, #F0B4AA 103.01%);
        }
        .av__man{
            background: linear-gradient(107.9deg, #4B96DC 0%, #96D2F5 103.01%);
        }
        .companyownerhe{
            background-size: cover;
            background-image: url({{asset('images/ownerhe.svg')}});
        }
        .companyownershe{
            background-size: cover;
            background-image: url({{asset('images/ownershe.svg')}});
        }

        .employeehe{
            background-size: cover;
            background-image: url({{asset('images/employeehe.svg')}});
        }
        .employeeshe{
            background-size: cover;
            background-image: url({{asset('images/employeeshe.svg')}});
            }
        .toggle input[type=checkbox]:checked + .toggle-icon{
            background: #2B2B2B;
        }
    </style>
    <ul class="d-flex ml-auto right-menu-icon" style="align-items: center;">
        <li>
            <a href="{{route('languageChange',['locale'=>'uz'])}}" class="{{!$language? 'active' : ''}} lang">O’zbek</a>
        </li>
        <li>
            <a href="{{route('languageChange',['locale'=>'ru'])}}" class="{{$language? 'active' : ''}} lang">Русский</a>
        </li>
        <li>
            <a href="#">
                <div class="card bg-transparent-card border-0 d-block">
                    <style>
                       
                        .toggle input[type=checkbox]:checked~.night {
                            display: none;
                        }
                        .toggle .day{
                            display: none;
                        }
                    </style>
                    
                    <div class="d-inline float-right p-0 m-0">
                        <label class="toggle toggle-dark p-0 m-0">
                            <svg width="23" class="day"  height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.5 6.77273C8.61455 6.77273 6.27273 9.11455 6.27273 12C6.27273 14.8855 8.61455 17.2273 11.5 17.2273C14.3855 17.2273 16.7273 14.8855 16.7273 12C16.7273 9.11455 14.3855 6.77273 11.5 6.77273ZM1.04545 13.0455H3.13636C3.71136 13.0455 4.18182 12.575 4.18182 12C4.18182 11.425 3.71136 10.9545 3.13636 10.9545H1.04545C0.470455 10.9545 0 11.425 0 12C0 12.575 0.470455 13.0455 1.04545 13.0455ZM19.8636 13.0455H21.9545C22.5295 13.0455 23 12.575 23 12C23 11.425 22.5295 10.9545 21.9545 10.9545H19.8636C19.2886 10.9545 18.8182 11.425 18.8182 12C18.8182 12.575 19.2886 13.0455 19.8636 13.0455ZM10.4545 1.54545V3.63636C10.4545 4.21136 10.925 4.68182 11.5 4.68182C12.075 4.68182 12.5455 4.21136 12.5455 3.63636V1.54545C12.5455 0.970455 12.075 0.5 11.5 0.5C10.925 0.5 10.4545 0.970455 10.4545 1.54545ZM10.4545 20.3636V22.4545C10.4545 23.0295 10.925 23.5 11.5 23.5C12.075 23.5 12.5455 23.0295 12.5455 22.4545V20.3636C12.5455 19.7886 12.075 19.3182 11.5 19.3182C10.925 19.3182 10.4545 19.7886 10.4545 20.3636ZM5.21682 4.24273C4.80909 3.835 4.14 3.835 3.74273 4.24273C3.335 4.65045 3.335 5.31955 3.74273 5.71682L4.85091 6.825C5.25864 7.23273 5.92773 7.23273 6.325 6.825C6.72227 6.41727 6.73273 5.74818 6.325 5.35091L5.21682 4.24273ZM18.1491 17.175C17.7414 16.7673 17.0723 16.7673 16.675 17.175C16.2673 17.5827 16.2673 18.2518 16.675 18.6491L17.7832 19.7573C18.1909 20.165 18.86 20.165 19.2573 19.7573C19.665 19.3495 19.665 18.6805 19.2573 18.2832L18.1491 17.175ZM19.2573 5.71682C19.665 5.30909 19.665 4.64 19.2573 4.24273C18.8495 3.835 18.1805 3.835 17.7832 4.24273L16.675 5.35091C16.2673 5.75864 16.2673 6.42773 16.675 6.825C17.0827 7.22227 17.7518 7.23273 18.1491 6.825L19.2573 5.71682ZM6.325 18.6491C6.73273 18.2414 6.73273 17.5723 6.325 17.175C5.91727 16.7673 5.24818 16.7673 4.85091 17.175L3.74273 18.2832C3.335 18.6909 3.335 19.36 3.74273 19.7573C4.15045 20.1545 4.81955 20.165 5.21682 19.7573L6.325 18.6491Z" fill="url(#paint0_linear_1019_867)"/>
                                <defs>
                                <linearGradient id="paint0_linear_1019_867" x1="0" y1="0.5" x2="28.3827" y2="9.66835" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#007382"/>
                                <stop offset="1" stop-color="#FF736E"/>
                                </linearGradient>
                                </defs>
                                </svg>
                                 
                            <input id="myCheckbox" type="checkbox">
                            <svg width="24" class="night" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.524 14.7212H19.532C20.176 14.7212 20.807 14.6622 21.418 14.5492L21.355 14.5592C20.209 18.6812 16.489 21.6572 12.074 21.6572H12.016H12.019C6.676 21.6512 2.346 17.3212 2.341 11.9782V11.9772C2.35353 9.87502 3.04445 7.83311 4.31093 6.15527C5.57742 4.47743 7.35179 3.25335 9.37 2.66516L9.439 2.64816C9.32971 3.24829 9.27548 3.85716 9.277 4.46716V4.47416C9.282 10.1322 13.867 14.7172 19.524 14.7222H19.525L19.524 14.7212ZM12.006 0.470162C11.8869 0.309424 11.7283 0.182162 11.5456 0.100698C11.3628 0.0192332 11.1622 -0.013674 10.963 0.005162H10.968C4.813 0.596162 0.034 5.72416 0 11.9762V11.9792C0.008 18.6142 5.385 23.9912 12.019 24.0002H12.08C18.323 24.0002 23.447 19.2142 23.985 13.1112L23.988 13.0662C24.0052 12.8762 23.9757 12.685 23.9019 12.5091C23.8282 12.3332 23.7125 12.178 23.565 12.0572L23.563 12.0552C23.4141 11.9326 23.2371 11.849 23.0479 11.8119C22.8587 11.7747 22.6632 11.7851 22.479 11.8422L22.487 11.8402L21.963 11.9962C21.1771 12.2538 20.355 12.3838 19.528 12.3812H19.521C17.4257 12.3788 15.4169 11.5454 13.9354 10.0638C12.4538 8.58221 11.6204 6.57344 11.618 4.47816V4.46016C11.618 3.43016 11.816 2.44616 12.176 1.54516L12.157 1.59816C12.2305 1.41171 12.2549 1.20948 12.2278 1.0109C12.2006 0.812309 12.1229 0.624037 12.002 0.464162L12.004 0.467162L12.006 0.470162Z" fill="url(#paint0_linear_928_816)"/>
                                <defs>
                                <linearGradient id="paint0_linear_928_816" x1="0" y1="0" x2="29.6096" y2="9.5617" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#4DB1B1"/>
                                <stop offset="1" stop-color="#4B96DC"/>
                                <stop offset="1" stop-color="#4B96DC"/>
                                </linearGradient>
                                </defs>
                                </svg>
                                
                        </label>
                        <script>
                            const checkbox = document.getElementById('myCheckbox')
    
                            checkbox.addEventListener('change', (event) => {
                            if (event.currentTarget.checked) {
                                localStorage.setItem('isDarkMode', true);
                               
                                document.querySelector('.toggle .day').style.display= 'block';
                            } else {
                               
                                localStorage.setItem('isDarkMode', false);
                                document.querySelector('.toggle .day').style.display= 'none';
                            }
                            })

                            if (localStorage.getItem('isDarkMode') === 'true') {
                                document.getElementById('myCheckbox').checked = true;
                                document.querySelector('body').classList.add('theme-dark');
                                document.querySelector('.toggle .day').style.display= 'block';
                            }else{
                                document.getElementById('myCheckbox').checked = false;
                                document.querySelector('.toggle .day').style.display= 'none';
                                document.querySelector('body').classList.remove('theme-dark');
                            }          
                            
                        </script>
                    </div>
                </div>
            </a>
        </li>
       
        <li><a href="{{route('profile')}}"><div style="width: 35px; height:35px;" class="avatar__new_user {{!$userinfo->gender? 'av__man' : 'av__girl'}} 
        @php if($userinfo->role=='Employee'){
            if($userinfo->gender){
                    echo 'employeeshe';
            }else{
                echo 'employeehe';
            }
           
           
        }else{
            if($userinfo->gender){
                    echo 'companyownershe';
            }else{
                echo 'companyownerhe';
            }
        }

    
    @endphp  rounded-circle"></div></a></li>
        
    </ul>
</div>