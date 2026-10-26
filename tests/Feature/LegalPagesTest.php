<?php

// Public legal pages linked from the Google OAuth consent screen (Drive backups).

it('serves the privacy policy publicly with the Google data disclosures', function () {
    $this->get('/privacy-policy')
        ->assertOk()
        ->assertSee('Privacy Policy')
        ->assertSee('https://www.googleapis.com/auth/drive.file')
        ->assertSee('Limited Use requirements');
});

it('serves the terms of service publicly', function () {
    $this->get('/terms-of-service')
        ->assertOk()
        ->assertSee('Terms of Service')
        ->assertSee(route('privacy'), false);
});

it('links both pages from the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('privacy'), false)
        ->assertSee(route('terms'), false);
});
