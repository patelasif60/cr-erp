<?php
$user = auth()->user();
$count = count($user->unreadNotifications);
$notifications = Auth::user()->unreadNotifications()->limit(10)->get();
?>


<div class="dropdown">
    <div class="badge-top-container" role="button" id="dropdownNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="badge badge-primary"><?php echo e($count); ?></span>
        <i class="i-Bell text-muted header-icon"></i>
    </div>
    <!-- Notification dropdown -->
    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none" aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href=<?php echo e($notification->data['url']); ?>>
                <div class="dropdown-item d-flex" style="<?php if($notification->read_at == ''): ?> background: #e4e6e9; <?php endif; ?>" onClick="MarkAsRead('<?php echo e($notification->id); ?>')">
                    <div class="notification-icon">
                        <span style='border: 1px solid #111!important;border-radius: 50%;padding: 5px 10px 2px 10px;'>
                            <?php echo ucwords(substr($notification->data['name'], 0, 1)); ?>
                        </span>
                    </div>
                    <div class="notification-details flex-grow-1">
                        <p class="m-0 d-flex align-items-center">
                            <span><?php echo e($notification->data['type']); ?></span>
                            <!-- <span class="badge badge-pill badge-primary ml-1 mr-1">new</span> -->
                            <!-- <span class="flex-grow-1"></span> -->
                            <span class="text-small text-muted ml-auto"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                        </p>
                        
                        <p class="text-small text-muted m-0"><b><?php echo Illuminate\Support\Str::limit(htmlspecialchars_decode($notification->data['note']), 50, ('...')); ?></b> </p>
                    </div>
                </div>
            </a>
            
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/layouts/notifications.blade.php ENDPATH**/ ?>