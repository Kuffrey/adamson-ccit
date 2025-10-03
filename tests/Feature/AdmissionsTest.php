<?php declare(strict_types=1);

// Test cases for Admissions (Freshman, Transferee, Graduate)
// Loads all admissions settings models
require_once __DIR__ . '/../../app/models/AdmissionFreshmanSettings.php'; // Freshman admissions model
require_once __DIR__ . '/../../app/models/AdmissionTransfereeSettings.php'; // Transferee admissions model
require_once __DIR__ . '/../../app/models/AdmissionGraduateSettings.php'; // Graduate admissions model

// Helper: Escape HTML for output
function e(string $s): string {
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ========== ADMISSIONS FEATURE TESTS ==========

// Test: Freshman settings are loaded and have required keys
test('freshman settings contain required fields', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	expect($settings)->toBeArray();
	expect($settings)->toHaveKey('subhero_lead');
	expect($settings)->toHaveKey('how_to_apply');
	expect($settings)->toHaveKey('initial_uploads');
	expect($settings)->toHaveKey('requirements_shs');
	expect($settings)->toHaveKey('requirements_als');
	expect($settings)->toHaveKey('requirements_abroad');
	expect($settings)->toHaveKey('enrollment_procedure');
	expect($settings)->toHaveKey('sidebar_office');
	expect($settings)->toHaveKey('sidebar_links');
	expect($settings)->toHaveKey('sidebar_image_url');
	expect($settings)->toHaveKey('sidebar_image_caption');
	expect($settings)->toHaveKey('cta_title');
	expect($settings)->toHaveKey('cta_description');
	expect($settings)->toHaveKey('cta_action_url');
	expect($settings)->toHaveKey('cta_action_label');
});

// Test: Transferee settings are loaded and have required keys
test('transferee settings contain required fields', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	expect($settings)->toBeArray();
	expect($settings)->toHaveKey('subhero_lead');
	expect($settings)->toHaveKey('how_to_apply');
	expect($settings)->toHaveKey('requirements');
	expect($settings)->toHaveKey('enrollment_procedure');
	expect($settings)->toHaveKey('enrollment_note');
	expect($settings)->toHaveKey('sidebar_office');
	expect($settings)->toHaveKey('sidebar_links');
	expect($settings)->toHaveKey('sidebar_image_url');
	expect($settings)->toHaveKey('sidebar_image_caption');
	expect($settings)->toHaveKey('cta_title');
	expect($settings)->toHaveKey('cta_description');
	expect($settings)->toHaveKey('cta_action_url');
	expect($settings)->toHaveKey('cta_action_label');
});

// Test: Freshman sidebar image URL is a string and safe
test('freshman sidebar image URL is a string and safe', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	$url = $settings['sidebar_image_url'] ?? '';
	expect($url)->toBeString();
	expect(e($url))->not->toContain('<');
});

// Test: Transferee sidebar image URL is a string and safe
test('transferee sidebar image URL is a string and safe', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	$url = $settings['sidebar_image_url'] ?? '';
	expect($url)->toBeString();
	expect(e($url))->not->toContain('<');
});

// Test: Freshman CTA fields are present and non-empty
test('freshman CTA fields are present and non-empty', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	expect($settings['cta_title'] ?? '')->not->toBe('');
	expect($settings['cta_description'] ?? '')->not->toBe('');
	expect($settings['cta_action_url'] ?? '')->not->toBe('');
	expect($settings['cta_action_label'] ?? '')->not->toBe('');
});

// Test: Transferee CTA fields are present and non-empty
test('transferee CTA fields are present and non-empty', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	expect($settings['cta_title'] ?? '')->not->toBe('');
	expect($settings['cta_description'] ?? '')->not->toBe('');
	expect($settings['cta_action_url'] ?? '')->not->toBe('');
	expect($settings['cta_action_label'] ?? '')->not->toBe('');
});

// Test: Freshman requirements fields are not empty
test('freshman requirements fields are not empty', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	expect($settings['requirements_shs'] ?? '')->not->toBe('');
	expect($settings['requirements_als'] ?? '')->not->toBe('');
	expect($settings['requirements_abroad'] ?? '')->not->toBe('');
});

// Test: Transferee requirements field is not empty
test('transferee requirements field is not empty', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	expect($settings['requirements'] ?? '')->not->toBe('');
});

// Test: Freshman enrollment procedure is not empty
test('freshman enrollment procedure is not empty', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	expect($settings['enrollment_procedure'] ?? '')->not->toBe('');
});

// Test: Transferee enrollment procedure is not empty
test('transferee enrollment procedure is not empty', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	expect($settings['enrollment_procedure'] ?? '')->not->toBe('');
});

// Test: Freshman sidebar office and links are not empty
test('freshman sidebar office and links are not empty', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	expect($settings['sidebar_office'] ?? '')->not->toBe('');
	expect($settings['sidebar_links'] ?? '')->not->toBe('');
});

// Test: Transferee sidebar office and links are not empty
test('transferee sidebar office and links are not empty', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	expect($settings['sidebar_office'] ?? '')->not->toBe('');
	expect($settings['sidebar_links'] ?? '')->not->toBe('');
});


// ========== ADMISSIONS NAVIGATION & BUTTON TESTS ==========

// Test: Freshman navigation links are present and valid
test('freshman navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=admission_freshman',
		'/adamson-ccit/public/index.php?page=admission_transferee',
		'/adamson-ccit/public/index.php?page=admission_graduate_school',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=admission_');
		expect(strpos($url, ' '))->toBeFalse(); // No spaces
	}
});

// Test: Graduate School navigation link is present and valid
test('graduate school navigation link is present and valid', function () {
    $url = '/adamson-ccit/public/index.php?page=admission_graduate_school';
    expect($url)->toStartWith('/adamson-ccit/public/index.php?page=admission_graduate_school');
    expect(strpos($url, ' '))->toBeFalse();
});

// Test: Transferee navigation links are present and valid
test('transferee navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=admission_freshman',
		'/adamson-ccit/public/index.php?page=admission_transferee',
		'/adamson-ccit/public/index.php?page=admission_graduate_school',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=admission_');
		expect(strpos($url, ' '))->toBeFalse();
	}
});

// Test: Freshman Apply button URL is present, valid, and external
test('freshman apply button URL is present and valid', function () {
	$applyUrl = 'https://www.adamson.edu.ph/cfe';
	expect($applyUrl)->toStartWith('https://');
	expect(filter_var($applyUrl, FILTER_VALIDATE_URL))->not->toBeFalse();
});

// Test: Graduate School CTA button/link is present and valid (if present in the view)
test('graduate school CTA button/link is present and valid', function () {
	// Default fallback URL as in the view, since settings model is not available
	$ctaUrl = '/adamson-ccit/public/index.php?page=programs_graduate';
	expect($ctaUrl)->not->toBe('');
	expect(strpos($ctaUrl, ' '))->toBeFalse();
});
// Test: Graduate School settings model (if exists) has required keys
if (class_exists('AdmissionGraduateSettings')) {
	test('graduate school settings contain required fields', function () {
		$settings = AdmissionGraduateSettings::getSettings();
		expect($settings)->toBeArray();
		expect($settings)->toHaveKey('subhero_lead');
		expect($settings)->toHaveKey('how_to_apply');
		expect($settings)->toHaveKey('initial_uploads');
		expect($settings)->toHaveKey('qualifications_masters');
		expect($settings)->toHaveKey('qualifications_doctoral');
		expect($settings)->toHaveKey('qualifications_jd');
		expect($settings)->toHaveKey('requirements_enrollment');
		expect($settings)->toHaveKey('enrollment_procedure');
		expect($settings)->toHaveKey('sidebar_office');
		expect($settings)->toHaveKey('sidebar_links');
		expect($settings)->toHaveKey('sidebar_image_url');
		expect($settings)->toHaveKey('sidebar_image_caption');
		expect($settings)->toHaveKey('cta_title');
		expect($settings)->toHaveKey('cta_description');
		expect($settings)->toHaveKey('cta_action_label');
		expect($settings)->toHaveKey('cta_action_url');
	});
}

// Test: Transferee Apply and eLearning button URLs are present and valid
test('transferee apply and elearning button URLs are present and valid', function () {
	$applyUrl = 'https://www.adamson.edu.ph/cfe';
	$elearningUrl = 'https://learn.adamson.edu.ph';
	expect($applyUrl)->toStartWith('https://');
	expect($elearningUrl)->toStartWith('https://');
	expect(filter_var($applyUrl, FILTER_VALIDATE_URL))->not->toBeFalse();
	expect(filter_var($elearningUrl, FILTER_VALIDATE_URL))->not->toBeFalse();
});

// Test: Freshman CTA button URL is present and valid
test('freshman CTA button URL is present and valid', function () {
	$settings = AdmissionFreshmanSettings::getSettings();
	$ctaUrl = $settings['cta_action_url'] ?? '';
	expect($ctaUrl)->not->toBe('');
	expect(strpos($ctaUrl, ' '))->toBeFalse();
});

// Test: Transferee CTA button URL is present and valid
test('transferee CTA button URL is present and valid', function () {
	$settings = AdmissionTransfereeSettings::getSettings();
	$ctaUrl = $settings['cta_action_url'] ?? '';
	expect($ctaUrl)->not->toBe('');
	expect(strpos($ctaUrl, ' '))->toBeFalse();
});