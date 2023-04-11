<x-mail::message>
# Hi {{$name}}

<h3>This is Your Code :</h3>

   <h1><strong>{{$code}}</strong></h1>


<h3>Use it To Reset Your Password.</h3>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
