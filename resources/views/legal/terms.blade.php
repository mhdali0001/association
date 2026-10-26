@extends('legal.layout')

@section('title', 'Terms of Service')
@section('description', 'Terms for using the beneficiary management system of ' . config('app.org_name_en') . '.')
@section('effective', 'October 27, 2026')

@section('content')
<p>These terms govern the use of the beneficiary management system of {{ config('app.org_name_en') }} (“the Foundation”), available at
    <a href="{{ url('/') }}">{{ parse_url(url('/'), PHP_URL_HOST) }}</a> (the “System”). By signing in to or using the System, you agree to these terms.</p>

<h2>1. The service</h2>
<p>The System is an internal tool that the Foundation’s authorized staff and volunteers use to manage beneficiary records, aid distribution
    and related reports. There is no public registration; accounts are created and managed by the Foundation’s administrators.</p>

<h2>2. Accounts and security</h2>
<ul>
    <li>Keep your password confidential and do not share your account with anyone.</li>
    <li>Use “Remember me on this device” only on a personal device, and remove saved accounts from any shared device.</li>
    <li>Tell an administrator immediately if you suspect that your account has been used without your permission.</li>
</ul>

<h2>3. Acceptable use</h2>
<ul>
    <li>Use the System only for the Foundation’s charitable work and only within the permissions of your role.</li>
    <li>Do not copy, export or disclose beneficiary information except as authorized by the Foundation.</li>
    <li>Do not attempt to bypass security controls, access other users’ accounts, or disrupt the System.</li>
    <li>Comply with all applicable laws when using the System.</li>
</ul>

<h2>4. Confidentiality</h2>
<p>Beneficiary information is confidential. You must protect it while you use the System and after your work with the Foundation ends.</p>

<h2>5. Google Drive backups</h2>
<p>The System stores backups of its database in the Foundation’s own Google Drive using Google OAuth with the
    <code>drive.file</code> permission. This integration is managed by the Foundation’s administrators and is described in our
    <a href="{{ route('privacy') }}">Privacy Policy</a>. Use of Google services is also subject to Google’s own terms.</p>

<h2>6. Availability</h2>
<p>The System is provided “as is” and “as available”. It may be unavailable from time to time for maintenance, updates or reasons outside
    our control, and we do not guarantee that it will be uninterrupted or error-free.</p>

<h2>7. Limitation of liability</h2>
<p>To the extent permitted by law, the Foundation is not liable for indirect or consequential losses arising from the use of, or inability
    to use, the System.</p>

<h2>8. Suspension and termination</h2>
<p>The Foundation’s administrators may suspend or remove an account at any time, for example when a user leaves the Foundation or does not
    follow these terms.</p>

<h2>9. Changes to these terms</h2>
<p>We may update these terms from time to time. The effective date at the top of this page shows when they were last changed. Continued use
    of the System after a change means you accept the updated terms.</p>

<h2>10. Contact</h2>
<p>For any question about these terms, contact {{ config('app.org_name_en') }}@if (config('app.contact_email'))
    at <a href="mailto:{{ config('app.contact_email') }}">{{ config('app.contact_email') }}</a>@endif.</p>
@endsection
