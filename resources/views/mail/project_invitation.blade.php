{{-- Ce fichier NE DOIT contenir QUE ceci --}}
<x-mail::message>
    # 🎯 Invitation à rejoindre un projet

    Bonjour,

    Vous avez été invité à rejoindre le projet **{{ $projectRole->project->title }}**
    en tant que **{{ $projectRole->role->name }}** sur la plateforme **{{ config('app.name') }}**.

    <x-mail::button :url="config('app.frontend_url').('/register?email=' . $email.'&token=' . $token)">
        Créer mon compte
    </x-mail::button>

    Si vous n'êtes pas à l'origine de cette invitation, vous pouvez ignorer ce message.

    Merci,
    L'équipe {{ config('app.name') }}
</x-mail::message>
