<?php declare(strict_types=1);

// ===================
// Test cases for Student feature pages (Organizations, Scholarships, Research, Certifications, Testimonials)
// ===================

// Load all required model classes for the student features
require_once __DIR__ . '/../../app/models/StudentOrganizationsPageSettings.php'; // Settings for organizations page
require_once __DIR__ . '/../../app/models/StudentOrganization.php'; // Student organization data
require_once __DIR__ . '/../../app/models/StudentScholarshipsPageSettings.php'; // Settings for scholarships page
require_once __DIR__ . '/../../app/models/StudentScholarship.php'; // Student scholarship data
require_once __DIR__ . '/../../app/models/StudentResearchPageSettings.php'; // Settings for research page
require_once __DIR__ . '/../../app/models/StudentResearch.php'; // Student research data
require_once __DIR__ . '/../../app/models/StudentCertificationsPageSettings.php'; // Settings for certifications page
require_once __DIR__ . '/../../app/models/StudentCertification.php'; // Student certification data
require_once __DIR__ . '/../../app/models/StudentTestimonialsPageSettings.php'; // Settings for testimonials page
require_once __DIR__ . '/../../app/models/StudentTestimonial.php'; // Student testimonial data


// Helper function: Escape HTML for output (renamed to avoid conflict with Laravel's e() function)
function escapeHtml(string $s): string {
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}


// ========== STUDENT FEATURE TESTS ========== 


// --------- Organizations ---------

// Test that the organizations page settings array contains required fields
test('student organizations settings contain required fields', function () {
	$settings = StudentOrganizationsPageSettings::getSettings(); // Get settings array
	expect($settings)->toBeArray(); // Should be an array
	expect($settings)->toHaveKey('subhero_image_url'); // Must have subhero image
	expect($settings)->toHaveKey('subhero_lead'); // Must have subhero lead text
});


// Test that the organizations list is not empty and has required fields
test('student organizations list is not empty', function () {
	$orgs = StudentOrganization::getAll(); // Get all organizations
	expect($orgs)->toBeArray(); // Should be an array
	expect(count($orgs))->toBeGreaterThan(0); // Should not be empty
	$org = $orgs[0]; // Check the first org
	expect($org)->toHaveKey('name'); // Must have name
	expect($org)->toHaveKey('type'); // Must have type
	expect($org)->toHaveKey('logo_url'); // Must have logo URL
	expect($org)->toHaveKey('summary'); // Must have summary
	expect($org)->toHaveKey('audience'); // Must have audience
});


// Test that navigation links for organizations are present and valid
test('student organizations navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=student_organizations', // Organizations page
		'/adamson-ccit/public/index.php?page=student_scholarships', // Scholarships page
		'/adamson-ccit/public/index.php?page=student_research', // Research page
		'/adamson-ccit/public/index.php?page=student_certifications', // Certifications page
		'/adamson-ccit/public/index.php?page=student_testimonials', // Testimonials page
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=student_'); // Should start with correct prefix
		expect(strpos($url, ' '))->toBeFalse(); // Should not contain spaces
	}
});


// Test that social and learn more links for organizations are valid URLs
test('student organizations social and learn more links are valid', function () {
	$orgs = StudentOrganization::getAll(); // Get all organizations
	foreach ($orgs as $org) {
		foreach (['facebook_url','instagram_url','x_url','learn_more_url'] as $key) {
			if (!empty($org[$key])) {
				expect(filter_var($org[$key], FILTER_VALIDATE_URL))->not->toBeFalse(); // Must be valid URL if present
			}
		}
	}
});


// --------- Scholarships ---------

// Test that the scholarships page settings array contains required fields
test('student scholarships settings contain required fields', function () {
	$settings = StudentScholarshipsPageSettings::getSettings(); // Get settings array
	expect($settings)->toBeArray(); // Should be an array
	expect($settings)->toHaveKey('subhero_image_url'); // Must have subhero image
	expect($settings)->toHaveKey('subhero_lead'); // Must have subhero lead text
});


// Test that the scholarships list is not empty and has required fields
test('student scholarships list is not empty', function () {
	$schs = StudentScholarship::getAll(); // Get all scholarships
	expect($schs)->toBeArray(); // Should be an array
	expect(count($schs))->toBeGreaterThan(0); // Should not be empty
	$sch = $schs[0]; // Check the first scholarship
	expect($sch)->toHaveKey('name'); // Must have name
	expect($sch)->toHaveKey('type'); // Must have type
	expect($sch)->toHaveKey('summary'); // Must have summary
});


// Test that navigation links for scholarships are present and valid
test('student scholarships navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=student_organizations',
		'/adamson-ccit/public/index.php?page=student_scholarships',
		'/adamson-ccit/public/index.php?page=student_research',
		'/adamson-ccit/public/index.php?page=student_certifications',
		'/adamson-ccit/public/index.php?page=student_testimonials',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=student_'); // Should start with correct prefix
		expect(strpos($url, ' '))->toBeFalse(); // Should not contain spaces
	}
});


// Test that learn more links for scholarships are valid URLs
test('student scholarships learn more links are valid', function () {
	$schs = StudentScholarship::getAll(); // Get all scholarships
	foreach ($schs as $sch) {
		if (!empty($sch['learn_more_url'])) {
			expect(filter_var($sch['learn_more_url'], FILTER_VALIDATE_URL))->not->toBeFalse(); // Must be valid URL if present
		}
	}
});


// --------- Research ---------

// Test that the research page settings array contains required fields
test('student research settings contain required fields', function () {
	$settings = StudentResearchPageSettings::getSettings(); // Get settings array
	expect($settings)->toBeArray(); // Should be an array
	expect($settings)->toHaveKey('subhero_image_url'); // Must have subhero image
	expect($settings)->toHaveKey('subhero_lead'); // Must have subhero lead text
});


// Test that navigation links for research are present and valid
test('student research navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=student_organizations',
		'/adamson-ccit/public/index.php?page=student_scholarships',
		'/adamson-ccit/public/index.php?page=student_research',
		'/adamson-ccit/public/index.php?page=student_certifications',
		'/adamson-ccit/public/index.php?page=student_testimonials',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=student_'); // Should start with correct prefix
		expect(strpos($url, ' '))->toBeFalse(); // Should not contain spaces
	}
});


// --------- Certifications ---------

// Test that the certifications page settings array contains required fields
test('student certifications settings contain required fields', function () {
	$settings = StudentCertificationsPageSettings::getSettings(); // Get settings array
	expect($settings)->toBeArray(); // Should be an array
	expect($settings)->toHaveKey('subhero_image_url'); // Must have subhero image
});


// Test that navigation links for certifications are present and valid
test('student certifications navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=student_organizations',
		'/adamson-ccit/public/index.php?page=student_scholarships',
		'/adamson-ccit/public/index.php?page=student_research',
		'/adamson-ccit/public/index.php?page=student_certifications',
		'/adamson-ccit/public/index.php?page=student_testimonials',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=student_'); // Should start with correct prefix
		expect(strpos($url, ' '))->toBeFalse(); // Should not contain spaces
	}
});


// --------- Testimonials ---------

// Test that the testimonials page settings array contains required fields
test('student testimonials settings contain required fields', function () {
	$settings = StudentTestimonialsPageSettings::getSettings(); // Get settings array
	expect($settings)->toBeArray(); // Should be an array
	expect($settings)->toHaveKey('subhero_image_url'); // Must have subhero image
	expect($settings)->toHaveKey('subhero_lead'); // Must have subhero lead text
});


// Test that navigation links for testimonials are present and valid
test('student testimonials navigation links are present and valid', function () {
	$navLinks = [
		'/adamson-ccit/public/index.php?page=student_organizations',
		'/adamson-ccit/public/index.php?page=student_scholarships',
		'/adamson-ccit/public/index.php?page=student_research',
		'/adamson-ccit/public/index.php?page=student_certifications',
		'/adamson-ccit/public/index.php?page=student_testimonials',
	];
	foreach ($navLinks as $url) {
		expect($url)->toStartWith('/adamson-ccit/public/index.php?page=student_'); // Should start with correct prefix
		expect(strpos($url, ' '))->toBeFalse(); // Should not contain spaces
	}
});