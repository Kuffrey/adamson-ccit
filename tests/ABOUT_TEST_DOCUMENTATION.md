# About Pages Test Documentation

## Overview
Comprehensive test suite for the About section pages (History and Vision & Mission) of the Adamson CCIT website.

## Test Coverage Summary

### 📊 **Total Tests: 32**

## Test Categories

### 🔗 **Page Loading Tests** (2 tests)
- `about history page loads successfully`
- `about vision mission page loads successfully`

**Validates:**
- HTTP 200 status codes
- Page content length > 1000 characters
- Proper page titles
- Main layout elements present

### 🧭 **Navigation Tests** (4 tests)
- `about history page has correct subnav structure`
- `about vision mission page has correct subnav structure`
- `about pages have correct active states in navigation`
- `about subnav links point to correct pages`

**Validates:**
- Subnav structure and ARIA labels
- Active state highlighting (`is-active` class)
- Correct `aria-current="page"` attributes
- Proper href attributes for navigation links

### 📄 **Content Structure Tests** (5 tests)
- `about history page has all required content sections`
- `about vision mission page has all required content sections`
- `about history page timeline structure is correct`
- `about vision mission departments have correct structure`

**Validates:**
- All major sections present (subhero, content, milestones, leaders, etc.)
- Timeline structure with proper semantic markup
- Department cards with correct IDs and structure
- Content grid layouts

### 📝 **Content Validation Tests** (4 tests)
- `about history page displays key content elements`
- `about vision mission page displays key content elements`
- `about history leadership section is present`
- `about history identity section is present`

**Validates:**
- Page titles and descriptions
- Key content sections and headings
- Leadership and identity sections
- Department information display

### 🔘 **Button and Link Tests** (4 tests)
- `about history page has working CTA button`
- `about vision mission page has working department buttons`
- `about vision mission page has working main CTA`
- `about history aside links work correctly`

**Validates:**
- CTA button presence and styling
- Department "See Programs" and "Explore BSCS" buttons
- Links to programs pages
- Cross-page navigation links

### ♿ **Accessibility Tests** (3 tests)
- `about pages have proper accessibility attributes`
- `about history timeline has proper semantic structure`
- `about vision mission has proper heading hierarchy`

**Validates:**
- ARIA labels and roles
- Screen reader compatibility
- Semantic HTML structure
- Proper heading hierarchy (h1-h4)
- Image alt attributes

### 📱 **Responsive Design Tests** (2 tests)
- `about pages have responsive viewport meta tag`
- `about pages have proper CSS grid and layout classes`

**Validates:**
- Viewport meta tag for mobile responsiveness
- CSS Grid and Flexbox layout classes
- Responsive container classes

### ⚡ **Performance and Validation Tests** (3 tests)
- `about pages load within reasonable time`
- `about pages have valid HTML structure`
- `about pages have proper CSS and asset links`

**Validates:**
- Page load time < 5 seconds
- Valid HTML5 document structure
- CSS stylesheet links
- Proper meta tags

### 🔄 **Cross-page Navigation Tests** (2 tests)
- `navigation between about pages works correctly`
- `about pages link to programs correctly`

**Validates:**
- Bi-directional navigation between History and Vision & Mission
- Links to undergraduate programs page
- Navigation consistency

### 🎨 **Content Integration Tests** (2 tests)
- `about pages display dynamic content correctly`
- `about pages have consistent branding and styling`

**Validates:**
- PHP processing (no raw PHP code in output)
- Consistent branding across both pages
- Uniform styling and layout patterns

## Page-Specific Features Tested

### 📚 **About History Page**
- **Timeline Section**: Milestones with proper semantic markup
- **Leadership Section**: Leaders and academic leads lists
- **Identity Section**: Values and identity grid
- **Origins Section**: Narrative content
- **Fact Sidebar**: "At a Glance" information

### 🎯 **About Vision & Mission Page**
- **Mission & Vision Cards**: College-wide statements
- **Departments Section**: IT & IS and Computer Science departments
- **Department Cards**: Individual vision, mission, and objectives
- **CTA Integration**: Links to programs pages

## Helper Functions

### `aboutPageRequest(string $method, string $page): array`
Performs HTTP requests to about pages with proper headers and timeout settings.

**Returns:** `[response_content, curl_info]`

## Running the Tests

```bash
# Run all about tests
vendor/bin/pest tests/Feature/AboutTest.php

# Run specific test groups
vendor/bin/pest tests/Feature/AboutTest.php --filter="loads successfully"
vendor/bin/pest tests/Feature/AboutTest.php --filter="navigation"
vendor/bin/pest tests/Feature/AboutTest.php --filter="content"
vendor/bin/pest tests/Feature/AboutTest.php --filter="accessibility"

# Run with verbose output
vendor/bin/pest tests/Feature/AboutTest.php -v
```

## Test Infrastructure

### **Base URL Configuration**
- Local development: `http://localhost/adamson-ccit/public/index.php`
- Pages tested: `about_history`, `about_vision_mission`

### **Assertion Patterns**
- HTTP status code validation
- Content presence verification
- CSS class and ID validation
- HTML structure verification
- Accessibility attribute checking

## Maintenance Notes

### **When to Update Tests**
- New content sections added to about pages
- Navigation structure changes
- New department information
- Accessibility requirements updates
- Performance benchmarks changes

### **Common Issues**
- Server configuration affecting local testing
- Dynamic content requiring database connection
- Image path validation
- CSS asset loading

## Coverage Analysis

### **Functional Coverage**
- ✅ Page loading and rendering
- ✅ Navigation and routing
- ✅ Content display and structure
- ✅ Button and link functionality
- ✅ Cross-page navigation

### **Technical Coverage**
- ✅ HTML validity and structure
- ✅ CSS and asset loading
- ✅ Accessibility compliance
- ✅ Responsive design elements
- ✅ Performance benchmarks

### **User Experience Coverage**
- ✅ Information architecture
- ✅ Content organization
- ✅ Visual hierarchy
- ✅ Interactive elements
- ✅ Multi-page workflows

This comprehensive test suite ensures the About section provides a reliable, accessible, and well-structured experience for users learning about CCIT's history, mission, and organizational structure.