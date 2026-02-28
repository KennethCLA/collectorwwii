<p><strong>Name:</strong> {{ $contact->name }}</p>
<p><strong>Email:</strong> {{ $contact->email }}</p>
<p><strong>Message:</strong></p>
<p>{{ $contact->message }}</p>
<p><strong>Sent at:</strong> {{ $contact->created_at->format('Y-m-d H:i') }}</p>
