<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class VirtualTourTest extends TestCase
{
    /**
     * Create a simple DOM parser to analyze HTML content
     */
    private function createDOMFromHTML($html)
    {
        $dom = new \DOMDocument();
        
        // Suppress warnings by using error handling functions
        libxml_use_internal_errors(true);
        
        // Add proper HTML5 doctype and wrap with html/body tags if needed
        if (!preg_match('/<\!DOCTYPE|<html/i', $html)) {
            $html = '<!DOCTYPE html><html><body>' . $html . '</body></html>';
        }
        
        // Load the HTML content
        $dom->loadHTML($html);
        
        // Clear errors
        libxml_clear_errors();
        
        return $dom;
    }
    
    /**
     * Helper to fetch the virtual tour page content
     */
    private function getVirtualTourContent()
    {
        // Start output buffering
        ob_start();
        
        // Include the virtual tour file
        $viewPath = __DIR__ . '/../../app/views/virtual_tour.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        }
        
        // Get content and clean buffer
        $content = ob_get_clean();
        return $content;
    }

    #[Test]
    public function test_virtual_tour_page_contains_iframe()
    {
        $content = $this->getVirtualTourContent();
        $this->assertNotEmpty($content, 'Virtual tour page should not be empty');
        
        $dom = $this->createDOMFromHTML($content);
        $iframes = $dom->getElementsByTagName('iframe');
        
        $this->assertGreaterThan(0, $iframes->length, 'Virtual tour page should contain at least one iframe');
        
        // Check that the first iframe has the expected attributes
        $iframe = $iframes->item(0);
        $this->assertStringContainsString('panoraven.com', $iframe->getAttribute('src'), 'Tour iframe should load from panoraven.com');
        $this->assertEquals('100%', $iframe->getAttribute('width'), 'Tour iframe should have width of 100%');
        $this->assertTrue($iframe->hasAttribute('allowfullscreen'), 'Tour iframe should have allowfullscreen attribute');
    }
    
    #[Test]
    public function test_virtual_tour_has_proper_accessibility_attributes()
    {
        $content = $this->getVirtualTourContent();
        $dom = $this->createDOMFromHTML($content);
        
        // Check iframe has a title
        $iframes = $dom->getElementsByTagName('iframe');
        $this->assertGreaterThan(0, $iframes->length);
        $iframe = $iframes->item(0);
        $this->assertTrue($iframe->hasAttribute('title'), 'Tour iframe should have a title for accessibility');
        
        // Check heading structure
        $h1Elements = $dom->getElementsByTagName('h1');
        $this->assertGreaterThan(0, $h1Elements->length, 'Page should have an h1 heading');
        
        // Check for alt text in images
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $this->assertTrue($img->hasAttribute('alt'), 'All images should have alt text');
            $this->assertNotEmpty($img->getAttribute('alt'), 'Alt text should not be empty');
        }
    }
    
    #[Test]
    public function test_virtual_tour_has_required_sections()
    {
        $content = $this->getVirtualTourContent();
        
        // Test for main structural components
        $this->assertStringContainsString('<section class="subhero">', $content, 'Page should have a subhero section');
        $this->assertStringContainsString('<section class="virtual-tour">', $content, 'Page should have a virtual tour section');
        $this->assertStringContainsString('<section class="cta">', $content, 'Page should have a CTA section');
        
        // Test for tour instructions
        $this->assertStringContainsString('tour-instructions', $content, 'Page should have tour instructions');
        
        // Check for CTA buttons
        $this->assertStringContainsString('Apply Now', $content, 'Page should have an Apply Now CTA');
        $this->assertStringContainsString('Learn More', $content, 'Page should have a Learn More CTA');
    }
    
    #[Test]
    public function test_virtual_tour_responsive_styles_present()
    {
        $content = $this->getVirtualTourContent();
        
        // Check for responsive media queries
        $this->assertStringContainsString('@media (max-width:', $content, 'Page should have responsive media queries');
        
        // Check for responsive iframe adjustments
        $this->assertStringContainsString('min-height:', $content, 'Iframe should have min-height property');
        
        // Check for grid layout
        $this->assertStringContainsString('display: grid', $content, 'Page should use CSS grid for layout');
        $this->assertStringContainsString('grid-template-columns:', $content, 'Grid should define template columns');
    }
    
    #[Test]
    public function test_virtual_tour_instruction_count()
    {
        $content = $this->getVirtualTourContent();
        $dom = $this->createDOMFromHTML($content);
        
        // Count instruction items
        $xpath = new \DOMXPath($dom);
        $instructionItems = $xpath->query("//div[contains(@class, 'instruction-item')]");
        
        // Should be 4 instruction items (Click & Drag, Zoom In/Out, Hotspots, Full Screen)
        $this->assertEquals(4, $instructionItems->length, 'Page should have exactly 4 instruction items');
    }
    
    #[Test]
    public function test_virtual_tour_has_valid_navigation_links()
    {
        $content = $this->getVirtualTourContent();
        $dom = $this->createDOMFromHTML($content);
        
        // Get all links
        $links = $dom->getElementsByTagName('a');
        $this->assertGreaterThan(0, $links->length, 'Page should have navigation links');
        
        // Check links have href attributes
        foreach ($links as $link) {
            $this->assertTrue($link->hasAttribute('href'), 'All links should have href attributes');
            $this->assertNotEmpty($link->getAttribute('href'), 'Link href should not be empty');
        }
        
        // Check for specific links
        $admissionLink = false;
        $aboutLink = false;
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, 'admission_freshman') !== false) {
                $admissionLink = true;
            }
            if (strpos($href, 'about_history') !== false) {
                $aboutLink = true;
            }
        }
        
        $this->assertTrue($admissionLink, 'Page should have a link to admission page');
        $this->assertTrue($aboutLink, 'Page should have a link to about/history page');
    }
    
    #[Test]
    public function test_virtual_tour_page_structure_validity()
    {
        $content = $this->getVirtualTourContent();
        
        // Check main structural elements
        $this->assertStringContainsString('<main>', $content, 'Page should have a main element');
        $this->assertStringContainsString('</main>', $content, 'Main element should be closed');
        
        // Check opening and closing tags match
        $openSectionTags = substr_count($content, '<section');
        $closeSectionTags = substr_count($content, '</section>');
        $this->assertEquals($openSectionTags, $closeSectionTags, 'Section opening and closing tags should match');
        
        $openDivTags = substr_count($content, '<div');
        $closeDivTags = substr_count($content, '</div>');
        $this->assertEquals($openDivTags, $closeDivTags, 'Div opening and closing tags should match');
    }

    #[Test]
    public function test_virtual_tour_loads_external_resources()
    {
        $content = $this->getVirtualTourContent();
        $dom = $this->createDOMFromHTML($content);
        
        // Check iframe has proper loading attribute for performance
        $iframes = $dom->getElementsByTagName('iframe');
        $this->assertGreaterThan(0, $iframes->length);
        $iframe = $iframes->item(0);
        $this->assertEquals('lazy', $iframe->getAttribute('loading'), 'Tour iframe should use lazy loading');
        
        // Check SVG icons are properly loaded
        $svgElements = $dom->getElementsByTagName('svg');
        $this->assertGreaterThan(0, $svgElements->length, 'Page should use SVG icons');
    }
}
