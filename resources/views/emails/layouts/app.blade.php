<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Notification' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f2f4f6; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding: 25px 0;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #1a202c; color: white; padding: 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 22px;">{{ $title ?? 'Valumate Notification' }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            {{-- Content goes here --}}
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px; text-align: center; background-color: #f7fafc; color: #718096; font-size: 13px;">
                            &copy; {{ date('Y') }} Valumate. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
