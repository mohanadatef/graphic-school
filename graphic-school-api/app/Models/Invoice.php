<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Enrollments\Models\Enrollment;
use Database\Factories\InvoiceFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'enrollment_id',
        'total_amount',
        'paid_amount',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    protected static function newFactory()
    {
        return InvoiceFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function updateStatus()
    {
        $totalPaid = $this->transactions()->where('status', 'success')->sum('amount');
        $this->paid_amount = $totalPaid;

        if ($totalPaid >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->status = 'partially_paid';
        } elseif ($this->due_date < now()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }
}

