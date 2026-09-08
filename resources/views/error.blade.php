@php
    // Rendered from the snapshot captured when the error occurred, never from
    // the live request: the mail may be queued and sent from another process.
    $errorDetails = $errorDetails ?? [];
    $authUser = $errorDetails['authUser'] ?? null;
@endphp
<x-mail::message>
## Environment :
# {{ config('app.url') ?? "" }}

## Error :
# {{ $exception ? $exception->getMessage() : "" }}

## File :
# {{ $exception ? $exception->getFile() : "" }}

## Line :
# {{ $exception ? $exception->getLine() : "" }}

## URL :
# {{ $errorDetails['url'] ?? "" }}

@component('mail::table')
| Request : | |
| --- | --- |
| **Method** | {{ $errorDetails['method'] ?? "" }} |
| **IP** | {{ $errorDetails['ip'] ?? "" }} |
| **User Agent** | {{ $errorDetails['userAgent'] ?? "" }} |
| **Referrer** | {{ $errorDetails['referrer'] ?? "" }} |
| **Request Time** | {{ $errorDetails['requestTime'] ?? "" }} |
| **Request URI** | {{ $errorDetails['requestUri'] ?? "" }} |
@endcomponent

@if($authUser)
@component('mail::table')
| Auth User : | |
| --- | --- |
| **ID** | {{ $authUser['id'] ?? "" }} |
| **Name** | {{ $authUser['name'] ?? "" }} |
| **Email** | {{ $authUser['email'] ?? "" }} |
@endcomponent
@else
@component('mail::table')
| Auth User : | |
| --- | --- |
| No Auth User | |
@endcomponent
@endif

@component('mail::button', ['url' => route('error.details', ['errorId' => $errorHash])])
See Error Details
@endcomponent

<p style="text-align: center; font-size: 14px; color: #555;">Copy the URL manually: <br>
<a href="{{ route('error.details', ['errorId' => $errorHash]) }}" style="color: #1d72b8; text-decoration: underline;">{{ route('error.details', ['errorId' => $errorHash]) }}</a></p>

## Trace :
@component('mail::panel')
{{ $exception || $stackTrace ? $stackTrace : "" }}
@endcomponent

</x-mail::message>

<style>
thead > tr > th {
text-align: left;
}
</style>
