@component('mail::message')
New Message from Contact Form

Name: {{ $data['name'] }}<br>
Email: {{ $data['email'] }}<br>
Subject: {{ $data['subject'] }}<br>
Message:<br>
{{ $data['message'] }}

@endcomponent
