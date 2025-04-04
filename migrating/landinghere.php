<?php

namespace App\ComponentSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class LandingHeroSchema extends UIComponentSchema
{
    /**
     * Component name used for component discovery
     */
    protected string $component = 'LandingHero';
    
    /**
     * Get example data for frontend consumption
     * This provides the exact format needed for UI components
     *
     * @return array
     */
    public function getExampleData(): array
    {
        return [
            'config' => [
                'gateways' => [
                    '/assets/gateways/paypal.png',
                    '/assets/gateways/skrill.png',
                    '/assets/gateways/venmo.png',
                    '/assets/gateways/visa.png'
                ],
                'bg_image' => '/assets/favicon.png',
                'bg_image2' => '/assets/question-icon.png',
                'payed_icon' => 'fa-chart-line',
                'payed_amount' => '$10,543,210',
                'payed_text' => 'Paid to Members',
                'badge_text' => 'What\'s Happening?',
                'title' => '
                    <span class="text-transparent bg-clip-text green-text-shadow">Transform</span> Your
                    <span class="text-transparent bg-clip-text green-text-shadow">Opinions</span>
                    <br>Into Cash With Paid Surveys
                ',
                'services' => [
                    [
                        'icon' => 'fa-check-circle',
                        'title' => 'Instant $1.00 Join Bonus',
                        'text' => 'Get rewarded by simply creating your profile.',
                        'color' => 'circle-gradient',
                        'icon_bg_color' => 'circle-bg-gradient',
                    ],
                    [
                        'icon' => 'fa-dollar-sign',
                        'title' => 'Fast Withdrawals',
                        'text' => 'Get paid the same day thanks to 8 hope payment promise.',
                        'color' => 'sign-gradient',
                        'icon_bg_color' => 'sign-bg-gradient',
                    ],
                    [
                        'icon' => 'fa-bolt',
                        'title' => 'Endless Online Earning Opportunities',
                        'text' => 'Make money with paid surveys, trial offers, playing games & more',
                        'color' => 'bolt-gradient',
                        'icon_bg_color' => 'bolt-bg-gradient',
                    ],
                ],
                'form_badge' => 'Free Join Bonus',
                'form_title' => 'Join Now',
            ]
        ];
    }

    /**
     * Define the component properties
     * This is used internally for validation, not for frontend data
     * 
     * @return array
     */
    public function properties(): array
    {
        // For simplicity, return an empty array since we're using getExampleData()
        return [];
    }

    /**
     * Get live data for the component
     * 
     * @return array
     */
    public function getLiveData(): array
    {
        // In a real implementation, this would fetch data from an API
        // Just return the example data for now
        return $this->getExampleData();
    }
}
