
<link rel="stylesheet" href="{{ asset('css/app.css')}}">
@livewireStyles

@livewireScripts
<script src="{{asset('js/app.js')}}"></script>

<div class="flex justify-center w-full">
    <a href="/" class="">Home</a>
    <a href="/up" class="">Upload</a>
</div>

@yield('content')
