<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MessageTemplate;
use App\Repositories\MemberSegmentationRepository;
use App\Repositories\MessageTemplateRepository;
use Carbon\Carbon;


class MemberSegmentationService
{

    protected MemberSegmentationRepository $memberSegmentationRepository;

    protected MessageTemplateRepository $messageTemplateRepository;
    
    public function __construct(

      MemberSegmentationRepository $memberSegmentationRepository,
        MessageTemplateRepository $messageTemplateRepository
    ) {
        $this->memberSegmentationRepository = $memberSegmentationRepository;
        $this->messageTemplateRepository = $messageTemplateRepository;
    }

    /**
     * Divide gym members into four engagement categories.
     */
    public function getSegmentedMembers(int $gymId): array
    {
        $members = $this->memberSegmentationRepository
            ->getMembersForSegmentation($gymId);

        $templates = $this->messageTemplateRepository
            ->getByGym($gymId)
            ->keyBy('category');

        $segments = [
            'new' => [],
            'inactive' => [],
            'expiring' => [],
            'expired' => [],
        ];

        $today = Carbon::today();

        foreach ($members as $member) {
            $subscriptions = $member->subscriptions;

            $activeSubscriptions = $subscriptions->filter(
                function ($subscription) use ($today) {
                    return $subscription->payment_status === 'paid'
                        && $subscription->start_date !== null
                        && $subscription->end_date !== null
                        && Carbon::parse($subscription->start_date)
                            ->startOfDay()->lte($today)
                        && Carbon::parse($subscription->end_date)
                            ->startOfDay()->gte($today);
                }
            );

            /*
             * Priority 1: Expired
             * A paid subscription has ended, and there is no
             * currently active paid subscription.
             */
            if (
                $activeSubscriptions->isEmpty()
                && $subscriptions->contains(
                    function ($subscription) use ($today) {
                        return $subscription->payment_status === 'paid'
                            && $subscription->end_date !== null
                            && Carbon::parse($subscription->end_date)
                                ->startOfDay()->lt($today);
                    }
                )
            ) {
                $subscription = $subscriptions
                    ->filter(function ($item) use ($today) {
                        return $item->payment_status === 'paid'
                            && $item->end_date !== null
                            && Carbon::parse($item->end_date)
                                ->startOfDay()->lt($today);
                    })
                    ->sortByDesc('end_date')
                    ->first();

                $segments['expired'][] = $this->formatMember(
                    $member,
                    'expired',
                    $subscription,
                    $templates->get('expired'),
                    $gymId
                );

                continue;
            }

            // Members without an active paid subscription
            // do not qualify for the other three categories.
            if ($activeSubscriptions->isEmpty()) {
                continue;
            }

            /*
             * If a member has multiple active subscriptions,
             * use the one ending last.
             */
            $subscription = $activeSubscriptions
                ->sortByDesc('end_date')
                ->first();

            /*
             * Priority 2: New
             * No attendance record and at least seven days
             * since the active subscription started.
             */
            if (
                $member->attendance_max_check_in_time === null
                && $subscription->start_date !== null
                && Carbon::parse($subscription->start_date)
                    ->startOfDay()->lte($today->copy()->subDays(7))
            ) {
                $segments['new'][] = $this->formatMember(
                    $member,
                    'new',
                    $subscription,
                    $templates->get('new'),
                    $gymId
                );

                continue;
            }

            /*
             * Priority 3: Inactive
             * The last attendance is older than the threshold
             * configured on the active subscription's plan.
             */
            $lastAttendance = $member->attendance_max_check_in_time;
            $threshold = $subscription->plan?->absence_threshold_days;

            if (
                $lastAttendance !== null
                && $threshold !== null
                && Carbon::parse($lastAttendance)
                    ->startOfDay()
                    ->lt($today->copy()->subDays((int) $threshold))
            ) {
                $segments['inactive'][] = $this->formatMember(
                    $member,
                    'inactive',
                    $subscription,
                    $templates->get('inactive'),
                    $gymId
                );

                continue;
            }

            /*
             * Priority 4: Expiring
             * The active subscription ends within five days,
             * including today.
             */
          $endDate = Carbon::parse($subscription->end_date)->startOfDay();

if (
    $endDate->gte($today)
    && $endDate->lte($today->copy()->addDays(5))
) {
    $segments['expiring'][] = $this->formatMember(
        $member,
        'expiring',
        $subscription,
        $templates->get('expiring'),
        $gymId
    );
}
        }

        return $segments;
    }

    /**
     * Prepare the member data and WhatsApp click-to-chat URL.
     */
    private function formatMember(
        Member $member,
        string $category,
        $subscription,
        ?MessageTemplate $template,
        int $gymId
    ): array {
        $gymName = $member->gym?->name ?? 'our gym';

        $endDate = $subscription?->end_date
            ? Carbon::parse($subscription->end_date)->format('Y-m-d')
            : '';

        $messageTemplate = $template?->template_text ?? '';

        $message = str_replace(
            ['{{name}}', '{{gym_name}}', '{{end_date}}'],
            [$member->name, $gymName, $endDate],
            $messageTemplate
        );

        $phone = $this->normalizePhoneNumber($member->phone);

       $whatsappUrl = $phone !== ''
          ? 'https://wa.me/' . $phone . '?text=' . rawurlencode($message)
               : null;
        return [
            'id' => $member->id,
            'name' => $member->name,
            'phone' => $member->phone,
            'email' => $member->email,
            'category' => $category,
            'subscription_end_date' => $endDate,
            'last_attendance' => $member->attendance_max_check_in_time,
            'message' => $message,
            'whatsapp_url' => $whatsappUrl,
        ];
    }

    /**
     * Normalize Egyptian phone numbers for WhatsApp links.
     */
    
private function normalizePhoneNumber(?string $phone): string
{
   $phone = preg_replace('/\D+/', '', $phone ?? '');

    if (!preg_match('/^01[0125][0-9]{8}$/', $phone)) {
        return '';
    }

    return '20' . substr($phone, 1);
}

}
