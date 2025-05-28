<x-mail::message>
# Activation de votre compte

Bonjour {{ $user->name }},

Merci de vous être inscrit(e) !
Pour activer votre compte, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous.

<x-mail::button :url="$url">
Activer mon compte
</x-mail::button>

Si vous n’avez pas créé de compte, aucune action n’est requise.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
