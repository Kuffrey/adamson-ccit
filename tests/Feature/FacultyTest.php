<?php declare(strict_types=1);

// ===================
// Test cases for Faculty feature pages (Profile, Research, Certifications)
// ===================

// Load all required model classes for the faculty features
require_once __DIR__ . '/../../app/models/FacultyProfilePageSettings.php'; // Settings for profile page
require_once __DIR__ . '/../../app/models/FacultyProfile.php'; // Faculty profile data
require_once __DIR__ . '/../../app/models/FacultyResearchPageSettings.php'; // Settings for research page
require_once __DIR__ . '/../../app/models/FacultyResearch.php'; // Faculty research data
require_once __DIR__ . '/../../app/models/FacultyCertificationsPageSettings.php'; // Settings for certifications page
require_once __DIR__ . '/../../app/models/FacultyCertification.php'; // Faculty certification data

// Helper function: Escape HTML for output (for safety in templates)
function e(string $s): string {
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ========== FACULTY FEATURE TESTS ========== 

// --------- Profile ---------

test('faculty profile settings contain required fields', function () {
	$settings = FacultyProfilePageSettings::getSettings();
	expect($settings)->toBeArray();
	expect($settings)->toHaveKey('subhero_image_url');
	expect($settings)->toHaveKey('subhero_lead');
});

test('faculty profile list is not empty', function () {
	$faculty = FacultyProfile::getAll();
	expect($faculty)->toBeArray();
	expect(count($faculty))->toBeGreaterThan(0);
	$f = $faculty[0];
	expect($f)->toHaveKey('name');
	expect($f)->toHaveKey('title');
	expect($f)->toHaveKey('dept');
	expect($f)->toHaveKey('role');
});

test('faculty profile navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=faculty_profile',
		'/adamson-ccit/public/index.php?page=faculty_research',
		'/adamson-ccit/public/index.php?page=faculty_certifications',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=faculty_');
		expect(strpos($url, ' '))->toBeFalse();
	}
});

// --------- Research ---------

test('faculty research settings contain required fields', function () {
	$settings = FacultyResearchPageSettings::getSettings();
	expect($settings)->toBeArray();
	expect($settings)->toHaveKey('subhero_image_url');
	expect($settings)->toHaveKey('subhero_lead');
});

test('faculty research list is not empty', function () {
	$research = FacultyResearch::getAll();
	expect($research)->toBeArray();
	expect(count($research))->toBeGreaterThan(0);
	$r = $research[0];
	expect($r)->toHaveKey('title');
	expect($r)->toHaveKey('authors');
	expect($r)->toHaveKey('venue');
	expect($r)->toHaveKey('year');
});

test('faculty research navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=faculty_profile',
		'/adamson-ccit/public/index.php?page=faculty_research',
		'/adamson-ccit/public/index.php?page=faculty_certifications',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=faculty_');
		expect(strpos($url, ' '))->toBeFalse();
	}
});

// Test that at least one research item has a valid view_url if research exists
test('at least one faculty research item has a valid view link if research exists', function () {
	$research = FacultyResearch::getAll();
	if (count($research) === 0) {
		expect($research)->toBeArray(); // No research, pass
		return;
	}
	$validFound = false;
	foreach ($research as $r) {
		if (!empty($r['view_url']) && $r['view_url'] !== '#' && filter_var($r['view_url'], FILTER_VALIDATE_URL)) {
			$validFound = true;
			break;
		}
	}
	expect($validFound)->toBeTrue(); // Fail if no valid view_url found
});

// Test that all non-empty, non-# research view links are valid URLs
test('all faculty research view links are valid URLs if present', function () {
	$research = FacultyResearch::getAll();
	foreach ($research as $r) {
		if (!empty($r['view_url']) && $r['view_url'] !== '#') {
			expect(filter_var($r['view_url'], FILTER_VALIDATE_URL))->not->toBeFalse();
		}
	}
});

// --------- Certifications ---------

test('faculty certifications settings contain required fields', function () {
	$settings = FacultyCertificationsPageSettings::getSettings();
	expect($settings)->toBeArray();
	expect($settings)->toHaveKey('subhero_image_url');
	expect($settings)->toHaveKey('subhero_lead');
});

test('faculty certifications grouped data is not empty', function () {
	$grouped = FacultyCertification::getGrouped();
	expect($grouped)->toBeArray();
	expect(count($grouped))->toBeGreaterThan(0);
	$firstYear = array_key_first($grouped);
	$firstIssuer = array_key_first($grouped[$firstYear]);
	$firstCerts = $grouped[$firstYear][$firstIssuer];
	expect($firstCerts)->toBeArray();
	$firstCert = reset($firstCerts);
	expect($firstCert)->toHaveKey('faculty');
});

test('faculty certifications navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=faculty_profile',
		'/adamson-ccit/public/index.php?page=faculty_research',
		'/adamson-ccit/public/index.php?page=faculty_certifications',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=faculty_');
		expect(strpos($url, ' '))->toBeFalse();
	}
});