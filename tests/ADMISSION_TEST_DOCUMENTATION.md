# Admission Pages Test Suite Documentation

## Overview
This document describes the comprehensive test suite created for the CCIT admission pages. The test suite includes 32 different test categories covering all aspects of admission page functionality.

## Test Categories

### 1. Accessibility Tests (3 tests)
- **admission freshman page loads successfully**: Verifies HTTP 200 response and content length
- **admission transferee page loads successfully**: Verifies HTTP 200 response and content length  
- **admission graduate school page loads successfully**: Verifies HTTP 200 response and content length

### 2. Page Structure Tests (3 tests)
- **admission freshman page has correct title and structure**: Validates title, subhero, and subnav elements
- **admission transferee page has correct title and structure**: Validates title, subhero, and subnav elements
- **admission graduate school page has correct title and structure**: Validates title, subhero, and subnav elements

### 3. Navigation Tests (3 tests)
Tests navigation links and active states across all admission pages:
- Freshman admission navigation
- Transferee admission navigation  
- Graduate school admission navigation

Expected elements:
- Correct href attributes for all admission page links
- Active state (`class="is-active"`) on current page
- `aria-current="page"` attribute on active link

### 4. Button Tests (3 tests)
Validates button functionality and external links:
- **Freshman page apply button**: Links to `https://www.adamson.edu.ph/cfe`
- **Transferee page buttons**: Apply button + eLearning button (`https://learn.adamson.edu.ph`)
- **Graduate school apply button**: Links to admissions portal

Expected attributes:
- `target="_blank"` and `rel="noopener"` for external links
- Correct CSS classes (`btn--solid-blue`, `btn--outline-blue`)

### 5. Content Section Tests (3 tests)
Verifies presence of required content sections:

**Freshman page sections:**
- Initial Uploads (Online Evaluation)
- Requirements for Freshmen Enrollment
- Senior High School Graduates
- ALS / Non-Formal Education
- Enrollment Procedure

**Transferee page sections:**
- Requirements for Application (Evaluation)
- Enrollment Procedure

**Graduate school sections:**
- Initial Uploads (Online Evaluation)
- Qualifications
- Master's Degree / Doctoral Program / Juris Doctor
- Requirements for Enrollment
- Enrollment Procedure

### 6. Sidebar Tests (3 tests)
Validates sidebar content across all pages:
- Office information (`class="fact"`)
- Quick Links section
- Photo with caption (`class="content__photo"` and `<figcaption>`)

### 7. CTA Section Tests (3 tests)
Verifies call-to-action sections:
- `class="cta"` container
- `class="cta__inner"` wrapper
- CTA buttons with appropriate classes

### 8. Accessibility Tests (3 tests)
Validates accessibility attributes:
- `aria-label="Admissions sub-navigation"`
- `role="list"` on navigation
- `aria-hidden="true"` on decorative elements
- `alt=""` attributes on images

### 9. Responsive Design Tests (2 tests)
- **Viewport meta tag**: Ensures responsive design support
- **CSS stylesheet**: Validates stylesheet inclusion

### 10. External Link Validation Tests (1 test)
Verifies external links have proper security attributes:
- `target="_blank"`
- `rel="noopener"`

### 11. Content Consistency Tests (2 tests)
- **Consistent HTML structure**: Validates common structural elements across pages
- **HTML5 structure**: Ensures proper doctype, language, and meta tags

### 12. Navigation Flow Tests (1 test)
Tests active state management across navigation:
- Freshman page shows Freshman as active
- Transferee page shows Transferee as active
- Graduate school page shows Graduate School as active

### 13. Performance Tests (2 tests)
- **Load time**: Pages must load within 3 seconds
- **Content length**: Validates reasonable content size (1KB - 100KB)

## Test Infrastructure

### Helper Function
```php
function admissionPageRequest($method, $page, $data = null) {
    $url = "http://localhost/adamson-ccit/public/index.php?page={$page}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [$response, $info];
}
```

### Pages Tested
- `admission_freshman`
- `admission_transferee` 
- `admission_graduate_school`

## Running the Tests

### Command
```bash
cd "c:\xampp\htdocs\adamson-ccit"
.\vendor\bin\pest tests/Feature/AdmissionTest.php
```

### Expected Results
- **Total Tests**: 32 test categories
- **Expected Assertions**: ~150+ assertions
- **Expected Duration**: 1-2 seconds
- **Expected Result**: All tests should pass

## Test Coverage

### Button Functionality
✅ Apply buttons link to correct URLs  
✅ External link security attributes  
✅ Button styling classes  
✅ Multiple button types (transferee page)  

### Content Display
✅ Required sections present on each page  
✅ Page-specific content (freshman vs transferee vs graduate)  
✅ Sidebar information and quick links  
✅ CTA sections with proper styling  

### Navigation
✅ Cross-page navigation links  
✅ Active state management  
✅ Accessibility attributes  
✅ Responsive design support  

### Structure & Accessibility
✅ Proper HTML5 structure  
✅ ARIA labels and roles  
✅ Image alt attributes  
✅ Semantic markup  

### Performance
✅ Reasonable load times  
✅ Appropriate content sizes  
✅ HTTP response validation  

## Maintenance

### Adding New Tests
1. Follow existing naming conventions (`admission [page] page [functionality]`)
2. Use the `admissionPageRequest()` helper function
3. Include both positive and negative test cases
4. Update this documentation

### Updating Expectations
When page content changes:
1. Update content expectations in affected tests
2. Ensure accessibility requirements remain met
3. Verify button URLs and external links
4. Test responsive design elements

## Known Issues
- Some tests may be sensitive to exact HTML formatting
- Large content responses may need regex patterns instead of exact string matches
- Performance tests depend on server response time

## Future Enhancements
- Add tests for form submission functionality
- Include tests for dynamic content loading
- Add visual regression testing
- Implement automated accessibility scanning