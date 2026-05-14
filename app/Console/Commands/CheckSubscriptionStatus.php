<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MobileUserSubscription;
use App\Models\Customer;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailTemplate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

class CheckSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription status and send reminders or stop service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now()->startOfDay();

        // 1. 7 days before subscription end
        $sevenDaysBefore = (clone $now)->addDays(7);
        $subscriptions7Before = MobileUserSubscription::whereDate('ends_at', $sevenDaysBefore)
            ->where('status', 'active')
            ->get();

        foreach ($subscriptions7Before as $subscription) {
            $this->sendEmail($subscription, 'upcoming_expiry');
        }

        // 2. 5 days after subscription end
        $fiveDaysAfter = (clone $now)->subDays(5);
        $subscriptions5After = MobileUserSubscription::whereDate('ends_at', $fiveDaysAfter)
            ->where('status', 'active')
            ->get();

        foreach ($subscriptions5After as $subscription) {
            $this->sendEmail($subscription, 'expired_reminder');
        }

        // 3. 7 days after subscription end (2 days after the second reminder)
        $sevenDaysAfter = (clone $now)->subDays(7);
        $subscriptions7After = MobileUserSubscription::whereDate('ends_at', $sevenDaysAfter)
            ->get();

        foreach ($subscriptions7After as $subscription) {
            $customer = $subscription->customer;
            if ($customer && $customer->app_access_enabled) {
                $customer->app_access_enabled = 0;
                $customer->save();
                $this->sendEmail($subscription, 'service_stopped');
                $this->info("Service stopped for customer: {$customer->email} (Subscription ended on: {$subscription->ends_at})");
            }
        }

        $this->info('Subscription check completed.');

        return 0;
    }

    /**
     * Send email to customer based on type.
     *
     * @param MobileUserSubscription $subscription
     * @param string $type
     * @return void
     */
    protected function sendEmail($subscription, $type)
    {
        $customer = $subscription->customer;
        if (!$customer) return;

        // Use super admin settings for mail
        $settings = Utility::getSettingById(1);
        $settingsArr = [];
        foreach ($settings as $setting) {
            $settingsArr[$setting->name] = $setting->value;
        }

        $appName = $settingsArr['company_name'] ?? env('APP_NAME');
        $endsAt = $subscription->ends_at->format('Y-m-d');

        $encryptedId = Crypt::encryptString($customer->id);

        $url = URL::temporarySignedRoute(
            'subscription.upgrade',
            now()->addDays(10),
            ['uid' => $encryptedId]
        );

        $messageBody = '';
        $title = '';
        $subject = '';

        switch ($type) {
            case 'upcoming_expiry':
                $subject = "Votre abonnement à $appName arrive bientôt à expiration";
                $title = "Expiration prochaine de l’abonnement";
                $messageBody = "Votre abonnement expire dans 7 jours. Veuillez le renouveler afin de continuer à profiter de nos services sans interruption.";
                break;

            case 'expired_reminder':
                $subject = "Votre abonnement à $appName a expiré";
                $title = "Abonnement expiré";
                $messageBody = "Votre abonnement a expiré il y a 5 jours. Ceci est un rappel amical pour effectuer votre paiement afin d’éviter la suspension du service.";
                break;

            case 'service_stopped':
                $subject = "Service suspendu - $appName";
                $title = "Service suspendu";
                $messageBody = "Votre abonnement a expiré et nous n’avons pas reçu votre paiement. Par conséquent, votre accès a été temporairement suspendu.";
                break;
        }

        // Render the blade view
        $htmlContent = view('email.subscription_status', [
            'app_name' => $appName,
            'customer_name' => $customer->name,
            'title' => $title,
            'message_body' => $messageBody,
            'upgrade_url' => $url,
            'ends_at' => $endsAt,
            'type' => $type
        ])->render();

        // Create a dummy template object for CommonEmailTemplate
        $templateObj = (object)[
            'from' => $appName,
            'subject' => $subject,
            'content' => $htmlContent
        ];

        try {
            config([
                'mail.default'                   => $settingsArr['mail_driver'] ?? '',
                'mail.mailers.smtp.host'         => $settingsArr['mail_host'] ?? '',
                'mail.mailers.smtp.port'         => $settingsArr['mail_port'] ?? '',
                'mail.mailers.smtp.encryption'   => $settingsArr['mail_encryption'] ?? '',
                'mail.mailers.smtp.username'     => $settingsArr['mail_username'] ?? '',
                'mail.mailers.smtp.password'     => $settingsArr['mail_password'] ?? '',
                'mail.from.address'              => $settingsArr['mail_from_address'] ?? '',
                'mail.from.name'                 => $settingsArr['mail_from_name'] ?? '',
            ]);

            Mail::to($customer->email)->send(new CommonEmailTemplate($templateObj, $settingsArr));
            $this->info("Email ($type) sent to: {$customer->email}");
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$customer->email}: " . $e->getMessage());
        }
    }
}
