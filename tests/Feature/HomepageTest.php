<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class HomepageTest extends TestCase
{
    // Helper function: Capture output of a PHP file
    private function captureOutput(string $filePath): string
    {
        $this->setupMockData();

        // Debugging: Verify global variables
        global $hero, $quickActions, $why, $spotlight, $programs, $partners, $news, $events, $cta;
        error_log('Hero: ' . print_r($hero, true));
        error_log('Programs: ' . print_r($programs, true));
        error_log('CTA: ' . print_r($cta, true));

        ob_start();
        include $filePath;
        return ob_get_clean();
    }

    // Helper function: Set up mock data for home.php
    private function setupMockData(): void
    {
        global $hero, $quickActions, $why, $spotlight, $programs, $partners, $news, $events, $cta, $navigationLinks;

        $hero = [
            'bg' => '/adamson-ccit/public/assets/images/career-bg.jpg',
            'eyebrow' => 'Welcome to Adamson CCIT',
            'title' => 'Empowering the next generation of innovators.',
            'subtitle' => 'Discover our programs and opportunities.',
            'actions' => [
                ['url' => '#', 'class' => 'btn--primary', 'label' => 'Learn More']
            ]
        ];

        $programs = [
            'title' => 'Explore Our Programs',
            'subtitle' => 'Undergraduate and Graduate Programs',
            'list' => [
                ['url' => '#', 'title' => 'Program 1', 'description' => 'Description of Program 1'],
                ['url' => '#', 'title' => 'Program 2', 'description' => 'Description of Program 2']
            ]
        ];

        $cta = [
            'title' => 'Join Our Community',
            'description' => 'Be part of our vibrant community.',
            'action_url' => '#',
            'action_label' => 'Apply Now',
            'contact_label' => 'Contact Us'
        ];

        $news = [
            'title' => 'Latest News',
            'view_all_url' => '#',
            'articles' => []
        ];

        $events = [];

        $navigationLinks = [
            ['url' => '/adamson-ccit/public/index.php?page=about', 'label' => 'About Us'],
            ['url' => '/adamson-ccit/public/index.php?page=programs', 'label' => 'Programs'],
            ['url' => '/adamson-ccit/public/index.php?page=admissions', 'label' => 'Admissions']
        ];
    }

    // --------- Hero Section ---------

    public function test_hero_section_is_present()
    {
        $output = $this->captureOutput(__DIR__ . '/../../app/views/home.php');

        $this->assertStringContainsString('Welcome to Adamson CCIT', $output);
        $this->assertStringContainsString('Empowering the next generation of innovators.', $output);
    }

    // --------- Featured Content ---------

    public function test_featured_content_is_present()
    {
        $output = $this->captureOutput(__DIR__ . '/../../app/views/home.php');

        $this->assertStringContainsString('Explore Our Programs', $output);
        $this->assertStringContainsString('Undergraduate and Graduate Programs', $output);
        $this->assertStringContainsString('Learn More', $output);
    }

    // --------- Call-to-Action (CTA) ---------

    public function test_cta_section_is_present()
    {
        $output = $this->captureOutput(__DIR__ . '/../../app/views/home.php');

        $this->assertStringContainsString('Join Our Community', $output);
        $this->assertStringContainsString('Apply Now', $output);
        $this->assertStringContainsString('Contact Us', $output);
    }

    // --------- Navigation Links ---------

    public function test_navigation_links_are_valid()
    {
        $output = $this->captureOutput(__DIR__ . '/../../app/views/home.php');
        error_log("Captured Output:\n" . $output);

        $this->assertStringContainsString('<a href="/adamson-ccit/public/index.php?page=about">About Us</a>', $output);
        $this->assertStringContainsString('<a href="/adamson-ccit/public/index.php?page=programs">Programs</a>', $output);
        $this->assertStringContainsString('<a href="/adamson-ccit/public/index.php?page=admissions">Admissions</a>', $output);
    }
}
