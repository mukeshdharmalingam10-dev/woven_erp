<?php

namespace App\Notifications;

use App\Models\CustomerOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CustomerOrderApprovalRequest extends Notification
{
    use Queueable;

    protected $customerOrder;

    /**
     * Create a new notification instance.
     */
    public function __construct(CustomerOrder $customerOrder)
    {
        $this->customerOrder = $customerOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Customer Order Approval Request - ' . $this->customerOrder->order_no)
            ->line('A new Customer Order requires your approval.')
            ->line('Order No: ' . $this->customerOrder->order_no)
            ->line('Order Date: ' . optional($this->customerOrder->order_date)->format('d-m-Y'))
            ->line('Tender No: ' . optional($this->customerOrder->tender)->tender_no)
            ->line('Net Amount: ₹' . number_format($this->customerOrder->net_amount ?? 0, 2))
            ->action('Review Order', route('approvals.index', ['form' => 'customer_orders']))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'customer_order_approval_request',
            'customer_order_id' => $this->customerOrder->id,
            'order_no' => $this->customerOrder->order_no,
            'order_date' => optional($this->customerOrder->order_date)->format('d-m-Y'),
            'tender_no' => optional($this->customerOrder->tender)->tender_no,
            'net_amount' => $this->customerOrder->net_amount ?? 0,
            'message' => 'Customer Order ' . $this->customerOrder->order_no . ' requires your approval.',
            'url' => route('approvals.index', ['form' => 'customer_orders']),
        ];
    }
}

