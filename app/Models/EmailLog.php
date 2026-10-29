<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmailLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'application_id',
        'mail_type',
        'to_email',
        'cc_email',
        'bcc_email',
        'subject',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Relationship dengan Application.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Spatie Activity Log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()

            ->logOnly([
                'application_id',
                'mail_type',
                'to_email',
                'cc_email',
                'bcc_email',
                'subject',
                'status',
                'error_message',
                'sent_at',
            ])

            ->logOnlyDirty()

            ->useLogName('email')

            ->setDescriptionForEvent(
                fn(string $eventName) =>
                    "Email {$this->mail_type} telah direkodkan dengan status {$this->status}"
            );
    }
}