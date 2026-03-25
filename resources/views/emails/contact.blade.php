<h1>Contact Form Submission</h1>

<p><strong>Nom du cabinet:</strong> {{ $data['cabinet-name'] }}</p>
<p><strong>Nom du contact:</strong> {{ $data['contact-name'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Téléphone:</strong> {{ $data['phone'] }}</p>
<p><strong>Type de demande:</strong> {{ implode(', ', $data['request-type']) }}</p>
<p><strong>Message:</strong></p>
<p>{{ $data['message'] }}</p>