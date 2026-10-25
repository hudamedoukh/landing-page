<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show the landing page.
     */
    public function home(): View
    {
        return view('pages.home', [
            'headline' => 'Ship ideas faster with Nimbus',
            'tagline' => 'The all-in-one workspace that helps small teams plan, build, and launch — without the chaos.',
            'features' => [
                ['title' => 'Plan together', 'description' => 'Shared roadmaps keep everyone aligned on what matters next.'],
                ['title' => 'Automate the boring', 'description' => 'Workflows that handle repetitive tasks so you can focus on building.'],
                ['title' => 'Launch with confidence', 'description' => 'Real-time insights show exactly how your release is performing.'],
            ],
        ]);
    }

    /**
     * Show the about page.
     */
    public function about(): View
    {
        return view('pages.about', [
            'foundedYear' => 2024,
            'mission' => 'We believe great products come from focused teams. Nimbus removes the busywork so you can do your best work.',
            'team' => [
                ['name' => 'Alex Rivera', 'role' => 'Co-founder & CEO'],
                ['name' => 'Sam Patel', 'role' => 'Co-founder & CTO'],
                ['name' => 'Jordan Lee', 'role' => 'Head of Design'],
            ],
        ]);
    }

    /**
     * Show the contact page.
     */
    public function contact(): View
    {
        return view('pages.contact', [
            'contactDetails' => [
                'Email' => 'hello@nimbus.example',
                'Phone' => '+1 (555) 010-2024',
                'Address' => '123 Cloud Street, San Francisco, CA',
            ],
        ]);
    }
}
