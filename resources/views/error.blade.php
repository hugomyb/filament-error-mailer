<x-mail::message>
## Environment :
# {{ env('APP_URL') ?? "" }}

## Error :
# {{ $exception ? $exception->getMessage() : "" }}

## File :
# {{ $exception ? $exception->getFile() : "" }}

## Line :
# {{ $exception ? $exception->getLine() : "" }}

## URL :
# {{ url(request()->getPathInfo()) ?? "" }}

@component('mail::table')
| Request : | |
| --- | --- |
| **Method** | {{ request()->method() }} |
| **IP** | {{ request()->ip() }} |
| **User Agent** | {{ request()->userAgent() }} |
| **Referrer** | {{ request()->header('referer') }} |
| **Request Time** | {{ \Carbon\Carbon::createFromTimestamp(request()->server('REQUEST_TIME'))->toDateTimeString() }} |
| **Request URI** | {{ request()->server('REQUEST_URI') }} |
@endcomponent

@if(auth()->check())
@component('mail::table')
| Auth User : | |
| --- | --- |
| **ID** | {{ auth()->id() }} |
| **Name** | {{ auth()->user()->name ?? "" }} |
| **Email** | {{ auth()->user()->email ?? "" }} |
@endcomponent
@else
@component('mail::table')
| Auth User : | |
| --- | --- |
| No Auth User | |
@endcomponent
@endif

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
