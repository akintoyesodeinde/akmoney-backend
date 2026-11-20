<script src="https://cdn.withpersona.com/dist/persona.js"></script>

<script>
Persona.create({
    templateId: "{{ $provider->api_key }}", // Persona calls this templateId
    referenceId: "{{ $user->id }}",
    environment: "{{ app()->environment('production') ? 'production' : 'sandbox' }}",
}).open();
</script>
