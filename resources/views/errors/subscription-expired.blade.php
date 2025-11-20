@extends('errors.master')

@section('title', 'Subscription Expired')
@section('main')

<main>
    <section class="error-wrapper p-0 m-0 text-center d-flex justify-content-center align-items-center flex-column">

      <div class="error-content p-0 m-0 text-center d-flex justify-content-center align-items-center flex-column">
        <!-- error image  -->
        <img src="{{asset('backend')}}/assets/images/error/error500.png" alt="Subscription Expired" />

        <!-- Head text  -->
        <h1 class="mt-30">Subscription Expired</h1>

        <!-- Error text   -->
        <p class="mt-10" style="max-width: 600px;">
            {{ session('error', 'Your subscription has expired. Please contact Telesom Sales to renew your subscription.') }}
        </p>

        <!-- Contact information -->
        <div class="mt-20">
            <p><strong>Contact Telesom Sales:</strong></p>
            <p>Email: sales@telesom.net | Phone: +252 61 5555555</p>
        </div>

        <!-- Back to login button  -->
        <div class="btn-back-to-homepage mt-28">
          <a href="{{route('login')}}" class="submit-button pv-16  btn ot-btn-primary">
            Back to Login
          </a>
        </div>
      </div>
    </section>

</main>
@endsection
