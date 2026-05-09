<x-mail::message>

<div style="text-align: center; padding: 24px 0;">
    <h1 style="font-size: 24px; font-weight: bold; color: #212055; margin: 0;">
        Good day, {{ $first_name }}!
    </h1>
    <p style="color: #333333; margin-top: 8px; font-size: 15px;">
        Use the code below to verify your email address.
    </p>
</div>

<div style="background-color: #212055; border-radius: 10px; padding: 32px; text-align: center; margin: 24px 0;">
    <p style="color: #c794e5; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin: 0 0 8px;">
        Verification Code
    </p>
    <p style="color: #fdfdfd; font-size: 42px; font-weight: bold; letter-spacing: 10px; margin: 0;">
        {{ $otp }}
    </p>
</div>

<p style="color: #333333; font-size: 14px; text-align: center;">
    This code expires in <strong>5 minutes</strong>.
</p>

<p style="color: #6147b0; font-size: 13px; text-align: center; margin-top: 16px;">
    If you did not create an account, please ignore this email.
</p>

<p style="color: #333333; font-size: 13px; text-align: center; margin-top: 24px;">
    Thanks,<br>
    <strong style="color: #212055;">{{ config('app.name') }}</strong>
</p>

</x-mail::message>