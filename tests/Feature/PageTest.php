<?php

test('home page shows the headline and features', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertViewIs('pages.home')
        ->assertViewHas('features')
        ->assertSee('Ship ideas faster with Nimbus')
        ->assertSee('Plan together');
});

test('about page shows the team', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertViewIs('pages.about')
        ->assertSee('Founded in 2024')
        ->assertSee('Alex Rivera');
});

test('contact page shows the contact details', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertViewIs('pages.contact')
        ->assertSee('hello@nimbus.example');
});

test('navigation contains all menu items', function () {
    $this->get(route('home'))
        ->assertSeeInOrder(['Home', 'About', 'Contact us']);
});
