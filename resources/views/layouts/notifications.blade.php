@php
$user = auth()->user();
$count = count($user->unreadNotifications);
$notifications = Auth::user()->unreadNotifications()->limit(10)->get();
@endphp


<div class="dropdown">
    <div class="badge-top-container" role="button" id="dropdownNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="badge badge-primary">{{ $count }}</span>
        <i class="i-Bell text-muted header-icon"></i>
    </div>
    <!-- Notification dropdown -->
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none" aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
    @foreach ($notifications as $notification)
            <a href={{ $notification->data['url'] }}>
                <div class="dropdown-item d-flex" style="@if($notification->read_at == '') background: #e4e6e9; @endif" onClick="MarkAsRead('{{ $notification->id }}')">
                    <div class="notification-icon">
                        <span style='border: 1px solid #111!important;border-radius: 50%;padding: 5px 10px 2px 10px;'>
                            <?php echo ucwords(substr($notification->data['name'], 0, 1)); ?>
                        </span>
                    </div>
                    <div class="notification-details flex-grow-1">
                        <p class="m-0 d-flex align-items-center">
                            <span>{{ $notification->data['type'] }}</span>
                            <!-- <span class="badge badge-pill badge-primary ml-1 mr-1">new</span> -->
                            <!-- <span class="flex-grow-1"></span> -->
                            <span class="text-small text-muted ml-auto">{{ $notification->created_at->diffForHumans() }}</span>
                        </p>
                        
                        <p class="text-small text-muted m-0"><b><?php echo Illuminate\Support\Str::limit(htmlspecialchars_decode($notification->data['note']), 50, ('...')); ?></b> </p>
                    </div>
                </div>
            </a>
            
        @endforeach
        <a href="/notifications">
            <div class="dropdown-item d-flex">
                <div class="notification-details flex-grow-1">
                    <p class="m-0 d-flex align-items-center">
                        Show all notifications
                    </p>
                    
                </div>
            </div>
        </a>
    </div>
</div>
