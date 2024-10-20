 <style>
    @media (max-width: 991.98px){
        .main-content {
            height: 100vh;
        }
        .app-footer{
            display: none;
        }
    }
    @media (max-width: 500px){
        .app-footer{
            display: flex;
            position: fixed;
            top: 0;
            bottom: auto;
            right: 19px;
            left: auto;
            z-index: 999;
            background: none;
            box-shadow: 0;
            box-shadow: none!important;
        }
        .app-footer .shadow-xss{
            box-shadow: none!important;
        }
        .app-footer a{
            padding: 0;
        }
    }
    </style>
        @php 
        $userinfofooter = \App\Models\User::find(auth()->user()? auth()->user()->id : 1);
        @endphp
        <div class="app-footer border-0 shadow-lg">
                  
                    <a href="{{route('profile')}}" class="nav-content-bttn w30 shadow-xss"><div style="width: 35px;
            height: 35px;" class="avatar__new_user {{!$userinfofooter->gender? 'av__man' : 'av__girl'}} 
                @php if($userinfofooter->role=='Employee'){
                    if($userinfofooter->gender){
                            echo 'employeeshe';
                    }else{
                        echo 'employeehe';
                    }
                   
                   
                }else{
                    if($userinfofooter->gender){
                            echo 'companyownershe';
                    }else{
                        echo 'companyownerhe';
                    }
                }
        
            
            @endphp  rounded-circle"></div></a>
                </div>
                <div class="app-header-search">
                    <form class="search-form">
                        <div class="form-group searchbox mb-0 border-0 p-1">
                            <input type="text" class="form-control border-0" placeholder="Search...">
                            <i class="input-icon">
                                <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                            </i>
                            <a href="#" class="ml-1 mt-1 d-inline-block close searchbox-close">
                                <i class="ti-close font-xs"></i>
                            </a>
                        </div>
                    </form>
                </div>