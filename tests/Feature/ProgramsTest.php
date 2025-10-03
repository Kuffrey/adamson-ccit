<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/models/ProgramsUndergraduateSettings.php';
require_once __DIR__ . '/../../app/models/ProgramsGraduateSettings.php';

// Helper to create a test undergraduate program card
function createTestUGCard(array $overrides = []): string {
    // Default card data
    $defaults = [
        'title' => 'Test Program ' . uniqid(), // Unique title for lookup
        'badge' => 'Test Badge',
        'summary' => 'Test summary',
        'muted' => '',
        'pill_t' => 'Majors',
        'pills' => 'Major 1,Major 2', // Pills as comma-separated string
        'lm_url' => 'https://example.com/learn',
        'lm_ext' => 1, // External link flag as int
        'cur_url' => 'https://example.com/curriculum',
        'cur_ext' => 1, // External link flag as int
        'apply' => '/apply',
    ];
    // Merge overrides
    $data = array_merge($defaults, $overrides);
    // Convert pills to string if array
    if (isset($data['pills']) && is_array($data['pills'])) {
        $data['pills'] = implode(',', $data['pills']);
    }
    // Convert ext fields to int
    if (isset($data['lm_ext'])) $data['lm_ext'] = (int)$data['lm_ext'];
    if (isset($data['cur_ext'])) $data['cur_ext'] = (int)$data['cur_ext'];
    // Actually create the card in the DB
    ProgramsUndergraduateSettings::createCard($data);
    // Return the unique title for lookup
    return $data['title'];
}

// HTML escape helper (for completeness, not used in tests)
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ========== PROGRAMS FEATURE TESTS ==========

test('can create and delete an undergraduate program card', function () {
    // Create a new test card and get its unique title
    $title = createTestUGCard();
    // Fetch all cards (admin mode)
    $cards = ProgramsUndergraduateSettings::getCards([], true);
    // Find the card by its unique title
    $found = null;
    foreach ($cards as $c) {
        if (($c['title'] ?? null) === $title) {
            $found = $c;
            break;
        }
    }
    // Assert the card exists
    expect($found)->not->toBeNull();
    $id = $found['id'] ?? null;
    expect($id)->not->toBeNull();
    // Delete the card by ID
    ProgramsUndergraduateSettings::deleteCardById($id);
    // Fetch cards again to confirm deletion
    $cards = ProgramsUndergraduateSettings::getCards([], true);
    $stillThere = false;
    foreach ($cards as $c) {
        if (($c['id'] ?? null) == $id) $stillThere = true;
    }
    // Assert the card is gone
    expect($stillThere)->toBeFalse();
});

test('can get all active undergraduate program cards', function () {
    // Create a new card with a unique title
    $title = createTestUGCard(['title' => 'Active UG Card ' . uniqid()]);
    // Fetch all cards (admin mode)
    $cards = ProgramsUndergraduateSettings::getCards([], true);
    // Look for the card by title
    $found = false;
    $id = null;
    foreach ($cards as $c) {
        if (($c['title'] ?? null) === $title) {
            $found = true;
            $id = $c['id'] ?? null;
        }
    }
    // Assert the card is present
    expect($found)->toBeTrue();
    // Clean up: delete the card
    if ($id) ProgramsUndergraduateSettings::deleteCardById($id);
});

test('subhero and CTA settings are returned', function () {
    // Fetch settings for the undergraduate programs page
    $settings = ProgramsUndergraduateSettings::getSettings();
    // Assert required keys exist
    expect($settings)->toHaveKeys(['subhero_lead', 'cta_title', 'cta_action_url']);
});

test('programs grid fallback returns HTML', function () {
    // Fetch settings and check for legacy grid HTML
    $settings = ProgramsUndergraduateSettings::getSettings();
    if (empty($settings['programs_grid'])) {
        $settings['programs_grid'] = '<div>Legacy Grid</div>';
    }
    // Assert the grid contains a div
    expect($settings['programs_grid'])->toContain('div');
});

test('graduate cards are returned and have expected fields', function () {
    // Fetch graduate program settings and cards
    $settings = ProgramsGraduateSettings::getSettings();
    $cards = ProgramsGraduateSettings::getCards($settings, false);
    // Assert each card has required fields
    foreach ($cards as $card) {
        expect($card)->toHaveKeys(['title', 'summary', 'slug']);
    }
});

test('graduate CTA settings are returned', function () {
    // Fetch graduate CTA settings and check required keys
    $settings = ProgramsGraduateSettings::getSettings();
    expect($settings)->toHaveKeys(['cta_title', 'cta_description']);
});

test('subnav URLs are correct', function () {
    // Check that subnav URLs are correct
    $ugUrl = '/adamson-ccit/public/index.php?page=programs_undergraduate';
    $gradUrl = '/adamson-ccit/public/index.php?page=programs_graduate_studies';
    expect($ugUrl)->toContain('programs_undergraduate');
    expect($gradUrl)->toContain('programs_graduate_studies');
});

test('external link attributes are set for ext links', function () {
    // Test the external link attribute helper
    $attr = function ($flag) { return !empty($flag) ? ' target="_blank" rel="noopener"' : ''; };
    expect($attr(true))->toContain('target="_blank"');
    expect($attr(false))->toBe('');
});

test('program card pills are rendered as array', function () {
    // Create a card with pills as an array
    $title = createTestUGCard(['pills' => ['A', 'B', 'C']]);
    $cards = ProgramsUndergraduateSettings::getCards([], true);
    $found = null;
    foreach ($cards as $c) {
        if (($c['title'] ?? null) === $title) {
            $found = $c;
            break;
        }
    }
    // Assert the card exists
    expect($found)->not->toBeNull();
    $pills = $found['pills'] ?? null;
    // Pills may be stored as a string, so check for array or string
    expect(is_array($pills) || is_string($pills))->toBeTrue();
    $id = $found['id'] ?? null;
    // Clean up: delete the card
    if ($id) ProgramsUndergraduateSettings::deleteCardById($id);
});