    <div class="main-header">
            <div class="logo">
                <!-- <img src="{{asset('assets/images/logo.png')}}" alt=""> -->
                <h3 style="padding-left: 30px;">Cranium</h3>
            </div>
            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- Search -->
                    <input type="text" id="search_text" name="search_text" placeholder="Search Product/Order" 
                        class="form-control" onkeypress="keyPressFunction(event);"/>
                    <i class="i-Magnifi-Glass1 header-icon" onclick="showOrderProductModal();"></i>
                <!-- Search End -->
                @if(auth()->user()->role != 6)
                <a href="#" class="btn btn-link btn-md m-1" onClick="GetModel('{{route('ProductWizardAjax')}}')">Product Wizard</a>
                <a class="btn btn-link btn-md m-1" data-toggle="modal" data-target="#myModal" href="#">Feedback</a>
                <a class="btn btn-link btn-md m-1" data-toggle="modal" data-target="#myHelpModal" href="#">Help</a>
                @endif
                <!-- Full screen toggle -->
                <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i> 
                @if(auth()->user()->role != 6)
                <!-- Notificaiton -->
                    @include('layouts.notifications')
                <!-- Notificaiton End -->
                @endif
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div  class="user col align-self-end">
                        <a id="userDropdown" data-toggle="dropdown">{{ Auth::user()->name }}</a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            @if(auth()->user()->role != 6)
                                <a class="dropdown-item" href="{{route('users.edit',Auth::user()->id)}}">Profile</a>
                            @endif
                            <a class="dropdown-item" href="{{route('logout')}}">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- header top menu end -->
        <script>
            function keyPressFunction(event) {
                if(event.key === 'Enter') {
                    showOrderProductModal();
                }
            }
            function showOrderProductModal() {

                var plainText = document.getElementById('search_text').value;
                if (plainText.trim().length <= 0) { 
                    document.getElementById('search_text').value = '';
                    return;
                }

                var searchText = btoa(unescape(encodeURIComponent(plainText)));
                var url = "{{ url('search_products_orders') }}/" + searchText;
                GetModel(url);
            }
        </script>