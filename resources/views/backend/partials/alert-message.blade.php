@if(Session::has('success'))
<script>
    // const Toast = Swal.mixin({
    // toast: true,
    // position: 'top-end',
    // showConfirmButton: false,
    // timer: 1500,
    // timerProgressBar: true,
    // didOpen: (toast) => {
    //     toast.addEventListener('mouseenter', Swal.stopTimer)
    //     toast.addEventListener('mouseleave', Swal.resumeTimer)
    // }
    // })

    Toast.fire({
        icon: 'success',
        title: '{{Session::get('success')}}'
    })
</script>
@endif
@if(Session::has('danger'))
<script>
    // const Toast = Swal.mixin({
    // toast: true,
    // position: 'top-end',
    // showConfirmButton: false,
    // timer: 1500,
    // timerProgressBar: true,
    // didOpen: (toast) => {
    //     toast.addEventListener('mouseenter', Swal.stopTimer)
    //     toast.addEventListener('mouseleave', Swal.resumeTimer)
    // }
    // })

    Toast.fire({
        icon: 'error',
        title: '{{Session::get('danger')}}'
    });

</script>
@endif

@if(Session::has('subscription_expired'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Subscription Expired',
        html: '<p style="font-size: 16px; margin-bottom: 15px;">{!! Session::get("subscription_expired") !!}</p>' +
              '<div style="margin-top: 20px; padding: 15px; background-color: #fee; border-left: 4px solid #d33; text-align: left;">' +
              '<strong style="color: #d33; font-size: 18px;">📞 Contact Telesom Sales</strong><br><br>' +
              '<strong>Email:</strong> sales@telesom.net<br>' +
              '<strong>Phone:</strong> +252 61 5555555' +
              '</div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: true,
        confirmButtonText: 'I Understand',
        confirmButtonColor: '#d33',
        width: '600px',
        customClass: {
            popup: 'swal2-border-radius',
            htmlContainer: 'swal2-html-container-custom'
        }
    });
</script>
@endif
