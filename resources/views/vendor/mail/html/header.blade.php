@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect Logo" style="max-height: 75px; width: auto;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>