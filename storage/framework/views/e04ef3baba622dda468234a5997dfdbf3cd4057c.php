
<?php $__env->startSection('body'); ?>
<script src="http://cdn.ckeditor.com/4.6.2/standard-all/ckeditor.js"></script>
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Email Template</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Dashboard
                        </li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="card-body p-4">
                <?php if(isset($emailTemplate)): ?>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(url('/admin/email-template/'.$emailTemplate->id)); ?>" id="validation">
                <?php echo method_field('PUT'); ?>
                <?php else: ?>
                <form method="post" action="<?php echo e(route('email-template.store')); ?>" enctype="multipart/form-data">
                    <?php endif; ?>
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        
                        <div class="mb-3 col-lg-12 col-md-6">
                            <label for="title" class="form-label">Template Title<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="title" placeholder="Title" id="title" value="<?php echo e(isset($emailTemplate)?$emailTemplate->title:''); ?>" required>
                        </div>
                        <div class="mb-3 col-lg-12 col-md-6 self">
                            <label for="message_email" class="form-label">Email Message<span class="text-danger">*</span></label>
                            <textarea class="form-control" name="message_email" id="message_email" cols="30" rows="10" required><?php echo e(isset($emailTemplate)?$emailTemplate->message_email:''); ?></textarea>
                        </div>
                        <div class="mb-3 col-lg-12 col-md-6">
                            <label for="image" class="form-label">Occasion Image<span class="text-danger"> (600 X 350 px)*</span></label>
                            <input class="form-control" type="file" name="image" id="image"<?php echo e(isset($emailTemplate)?'':'required'); ?>>
                        </div>
                        <div class="mb-3 col-lg-12 col-md-6">
                            <label for="title" class="form-label">Footer Message<span class="text-danger">*</span></label>
                            <textarea class="form-control" name="footer_message" id="footer_message" cols="30" rows="10" required><?php echo e(isset($emailTemplate)?$emailTemplate->footer_message:''); ?></textarea>
                        </div>
                       
                        
                   
                        <div class="mb-3 col-lg-6">
                            
                            <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                        </div>
                    </div>
                </form>
               
            </div>
            <!--end::Row-->               
                <!-- /.Start col -->
            </div>
            <!-- /.row (main row) -->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('script'); ?>
    

<script>
    function couponRedeem() {
        // Get the selected radio button
        var selectedValue = document.querySelector('input[name="coupon"]:checked').value;

        // You can perform actions based on the selected value
        if (selectedValue === 'yes') {
            // Code to handle when "YES" is selected
            $('.coupon_code').show();
        } else if (selectedValue === 'no') {
            // Code to handle when "NO" is selected
            $('.coupon_code').hide();
        }
    }
</script>



  
<script>
      function geftCardSendToOther(){
        var recipientRadios = document.getElementsByName('recipient');
    
        for (var i = 0; i < recipientRadios.length; i++) {
          // alert(recipientRadios[i].value);
        if (recipientRadios[i].value=='other' && recipientRadios[i].checked) {
          // Display the selected value
          $('.self').css({'display':'block'});
          break; // Exit the loop since we found the selected radio button
        }
        if (recipientRadios[i].value=='self' && recipientRadios[i].checked) {
          // Display the selected value
          $('.self').css({'display':'none'});
          break; // Exit the loop since we found the selected radio button
        } 
      }
      }
    
     
      </script>
  <script>

$(document).ready(function() {
  $('.summernote').summernote({
    popover: {
      image: [

        // This is a Custom Button in a new Toolbar Area
        ['custom', ['examplePlugin']],
        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ]
    }
  });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\MedsapGiftCardNew\resources\views/email_template/create.blade.php ENDPATH**/ ?>