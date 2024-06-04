@component('mail::message')
# There is a new {{$type . ': ' . $name}}. Register in our system.

@component('mail::button', ['url' => $url])
View
@endcomponent

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
