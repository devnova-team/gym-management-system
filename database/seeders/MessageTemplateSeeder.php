<?php

namespace Database\Seeders;
use App\Models\Gym;
use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    
 
    /**
     * Create default message templates for every existing gym.
     */
    public function run(): void
    {
        $defaultTemplates = [
            'new' => 'Hi {{name}}! Welcome to {{gym_name}}. We are happy to have you with us!',

            'inactive' => 'Hi {{name}}, we have not seen you at {{gym_name}} recently. We hope to see you back soon!',

            'expiring' => 'Hi {{name}}, your membership at {{gym_name}} expires on {{end_date}}. Renew soon to continue your workouts!',

            'expired' => 'Hi {{name}}, your membership at {{gym_name}} has expired. Contact us to renew and get back to your workouts!',
        ];

        $gyms = Gym::query()->get();

        foreach ($gyms as $gym) {
            foreach ($defaultTemplates as $category => $templateText) {
                MessageTemplate::firstOrCreate(
                    [
                        'gym_id' => $gym->id,
                        'category' => $category,
                    ],
                    [
                        'template_text' => $templateText,
                    ]
                );
            }
        }

        $this->command->info(
            "Default message templates created for {$gyms->count()} gyms."
        );
    }
}
