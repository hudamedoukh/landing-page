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
            'headline' => __('Ship ideas faster with Nimbus'),
            'tagline' => __('The all-in-one workspace that helps small teams plan, build, and launch — without the chaos.'),
            'features' => [
                ['title' => __('Plan together'), 'description' => __('Shared roadmaps keep everyone aligned on what matters next.')],
                ['title' => __('Automate the boring'), 'description' => __('Workflows that handle repetitive tasks so you can focus on building.')],
                ['title' => __('Launch with confidence'), 'description' => __('Real-time insights show exactly how your release is performing.')],
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
            'mission' => __('We believe great products come from focused teams. Nimbus removes the busywork so you can do your best work.'),
            'team' => [
                ['name' => 'Alex Rivera', 'role' => __('Co-founder & CEO')],
                ['name' => 'Sam Patel', 'role' => __('Co-founder & CTO')],
                ['name' => 'Jordan Lee', 'role' => __('Head of Design')],
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
                ['label' => __('Email'), 'value' => 'hello@nimbus.example', 'isLeftToRight' => true],
                ['label' => __('Phone'), 'value' => '+1 (555) 010-2024', 'isLeftToRight' => true],
                ['label' => __('Address'), 'value' => __('123 Cloud Street, San Francisco, CA'), 'isLeftToRight' => false],
            ],
        ]);
    }
}
