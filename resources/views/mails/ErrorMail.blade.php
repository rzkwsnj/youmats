@component('mail::message')
There is a new issue appearing at level : {{ $level }}.

Error :  {{ $level . ' - ' . $message }}

please fix it as soon as possible.

@component('mail::button', ['url' => $url])
View Error Page
@endcomponent

Thanks,<br>
{{ config('app.name') }} System
@endcomponent

