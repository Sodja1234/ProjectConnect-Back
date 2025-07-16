<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Invitation à rejoindre un projet</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            color: #333;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .email-container {
            width: 100%;
            max-width: 500px;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        h1 {
            color: #000;
            font-size: 22px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .content {
            text-align: center;
            margin-bottom: 25px;
            color: #444;
            line-height: 1.6;
            font-size: 15px;
        }
        .highlight {
            color: #000;
            font-weight: 600;
        }
        .btn-container {
            margin: 30px 0;
        }
        .btn {
            background-color: #000;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .btn:hover {
            background-color: #333;
        }
        .disclaimer {
            font-size: 13px;
            color: #888;
            text-align: center;
            margin: 25px 0 30px 0;
            line-height: 1.5;
            font-style: italic;
        }
        .footer {
            margin-top: 0;
            padding-top: 0;
            color: #666;
            font-size: 13px;
        }
        .signature {
            color: #000;
            font-weight: 600;
            margin-top: 5px;
            display: block;
        }
        .copyright {
            margin-top: 10px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>Invitation à rejoindre un projet</h1>
    </div>

    <div class="content">
        <p>Bonjour <span class="highlight">{{ $projectRole->user->name ?? 'cher utilisateur' }}</span>,</p>

        <p>
            Vous avez été invité(e) à rejoindre le projet<br>
            <span class="highlight">« {{ $projectRole->project->title }} »</span> en tant que<br>
            <span class="highlight">{{ $projectRole->role->name }}</span> sur notre plateforme<br>
            <span class="highlight">{{ config('app.name') }}</span>.
        </p>
    </div>

    <div class="btn-container">
        <a href="{{ $invitationUrl }}" class="btn">
            Créer mon compte
        </a>
    </div>

    <p class="disclaimer">
        Si vous n'êtes pas à l'origine de cette invitation, vous pouvez ignorer ce message.
    </p>

    <div class="footer">
        <p>
            Cordialement,<br>
            <span class="signature">L'équipe {{ config('app.name') }}</span>
        </p>
        <p class="copyright">
            © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </p>
    </div>
</div>
</body>
</html>
