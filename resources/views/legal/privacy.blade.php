@extends('legal.layout')

@section('title', 'Privacy Policy')
@section('description', 'How the beneficiary management system of ' . config('app.org_name_en') . ' handles personal information and Google user data.')
@section('effective', 'October 27, 2026')

@section('content')
<p>{{ config('app.org_name_en') }} (“the Foundation”, “we”, “us”) operates the beneficiary management system available at
    <a href="{{ url('/') }}">{{ parse_url(url('/'), PHP_URL_HOST) }}</a> (the “System”). This policy explains what information the System
    processes, how we use it, and how the System uses Google user data.</p>

<h2>1. Who uses the System</h2>
<p>The System is an internal tool for the Foundation’s authorized staff and volunteers. Accounts are created by the Foundation’s
    administrators; there is no public sign-up.</p>

<h2>2. Information we process</h2>
<ul>
    <li><strong>Staff accounts:</strong> name, email address, phone number, role, and a password stored as a one-way hash.</li>
    <li><strong>Beneficiary records:</strong> information entered by authorized staff to provide assistance, such as names, national ID numbers,
        phone numbers, addresses, family, housing and health circumstances, field visit notes, aid amounts and payment records.</li>
    <li><strong>Activity logs:</strong> actions performed in the System, with the date and time, IP address and browser information, kept for accountability.</li>
    <li><strong>Cookies:</strong> only cookies that are strictly necessary to sign in and keep a session, and — when a user chooses
        “Remember me on this device” — to remember the saved accounts on that device. We do not use advertising or analytics cookies.</li>
</ul>

<h2>3. How we use information</h2>
<p>We use this information to manage beneficiary records, plan and distribute aid, verify eligibility, prevent duplicate assistance,
    produce internal reports, and keep the System secure. We do not sell personal information and we do not use it for advertising.</p>

<h2>4. Google user data</h2>
<p>The System includes an automatic backup feature that connects to the Foundation’s own Google account through Google OAuth.</p>
<ul>
    <li><strong>Permission requested:</strong> only <code>https://www.googleapis.com/auth/drive.file</code>, which gives access only to files the System itself creates.</li>
    <li><strong>What the System does with it:</strong> it uploads copies of the System’s database backup files to a dedicated folder in that Google Drive,
        and deletes old backup files that it created, according to the retention schedule in section 6.</li>
    <li><strong>What the System does not do:</strong> it does not view, read, list, modify or delete any other files in Google Drive, and it does not access
        Gmail, Contacts, Calendar or any other Google service.</li>
    <li><strong>Sharing:</strong> Google user data is not sold, not shared with third parties, not used for advertising, and not used to train artificial
        intelligence or machine learning models.</li>
    <li><strong>Credentials:</strong> the OAuth token is stored only in the server’s private configuration and is available only to the Foundation’s
        administrators. Access can be revoked at any time from the Google Account’s
        <a href="https://myaccount.google.com/permissions" rel="noopener">third-party access settings</a>.</li>
</ul>
<p class="note">The System’s use and transfer of information received from Google APIs adheres to the
    <a href="https://developers.google.com/terms/api-services-user-data-policy" rel="noopener">Google API Services User Data Policy</a>,
    including the Limited Use requirements.</p>

<h2>5. Sharing of information</h2>
<p>We share information only with service providers that host and store the System on our behalf (our web hosting provider, and Google Drive for
    backup storage), or when required by law or needed to protect the safety of people. We do not sell or rent personal information.</p>

<h2>6. Retention</h2>
<ul>
    <li>Beneficiary records are kept for as long as needed for the Foundation’s charitable work and record-keeping. When a record is deleted,
        a copy is kept in an internal archive for audit purposes.</li>
    <li>Database backups: all backups are kept for 7 days, then one daily backup for 16 days, one weekly backup for 8 weeks, one monthly backup
        for 4 months, and one yearly backup for 2 years. Older backups are deleted automatically.</li>
    <li>Saved accounts on a device expire after 30 days without use, and immediately when the account’s password is changed.</li>
</ul>

<h2>7. Security</h2>
<p>The System is served over HTTPS, passwords are stored as one-way hashes, and access is limited to authorized accounts with role-based
    permissions; changes made by non-administrator accounts require an administrator’s approval. No method of transmission or storage is
    completely secure, but we work to protect the information we hold.</p>

<h2>8. Your rights</h2>
<p>Beneficiaries and staff members may ask to access, correct or delete their information by contacting the Foundation. We will respond in
    line with applicable law and our record-keeping obligations.</p>

<h2>9. Children</h2>
<p>The System is not intended for use by children. Beneficiary records may include information about family members, including minors,
    provided by their guardians in order to receive assistance.</p>

<h2>10. Changes to this policy</h2>
<p>We may update this policy from time to time. The effective date at the top of this page shows when it was last changed.</p>

<h2>11. Contact</h2>
<p>For any question about this policy or your information, contact {{ config('app.org_name_en') }}@if (config('app.contact_email'))
    at <a href="mailto:{{ config('app.contact_email') }}">{{ config('app.contact_email') }}</a>@endif.</p>
@endsection
