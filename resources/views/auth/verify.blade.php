@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card">

                    <div class="card-header poppins-regular py-3">{{ __('Verify Your Email Address') }}</div>

                    <div class="card-body py-5">

                        @if (session('resent'))
                            <div class="text-success mb-4" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        <p>Enviamos um link de verificação para o seu e-mail. </p>


                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <p class="">Se você não recebeu o e-mail,
                                <button type="submit"
                                    class="btn btn-link m-0 p-0 align-baseline">{{ __('click here to request another') }}</button>.
                            </p>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
